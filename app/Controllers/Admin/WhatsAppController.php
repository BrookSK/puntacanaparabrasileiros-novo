<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\WhatsappInstance;
use App\Models\WhatsappContact;
use App\Models\WhatsappMessage;
use App\Models\WhatsappLabel;
use App\Models\CrmBoard;
use App\Services\EvolutionApi;
use App\Middleware\WhatsAppMiddleware;

/**
 * Controller do módulo WhatsApp.
 * Gerencia instâncias, chat, contatos, mensagens, respostas rápidas e webhook.
 */
class WhatsAppController extends Controller
{
    private WhatsappInstance $instanceModel;
    private WhatsappContact $contactModel;
    private WhatsappMessage $messageModel;
    private WhatsappLabel $labelModel;

    public function __construct()
    {
        parent::__construct();
        $this->instanceModel = new WhatsappInstance();
        $this->contactModel = new WhatsappContact();
        $this->messageModel = new WhatsappMessage();
        $this->labelModel = new WhatsappLabel();
    }

    // ═══════════════════════════════════════════════
    // INSTÂNCIAS
    // ═══════════════════════════════════════════════

    /**
     * Tela de configuração/gerenciamento de instâncias.
     */
    public function index(Request $request, Response $response): void
    {
        $instances = $this->instanceModel->allWithUser();
        $users = $this->db->fetchAll(
            "SELECT id, first_name, last_name, role FROM users 
             WHERE role IN ('superadmin','admin','attendant','whatsapp_agent','comercial') 
             AND status = 'active' ORDER BY first_name"
        );

        $this->view('admin/whatsapp/index', [
            'instances' => $instances,
            'users' => $users,
            'pageTitle' => 'WhatsApp — Instâncias',
        ], 'admin');
    }

    /**
     * Cria nova instância.
     */
    public function createInstance(Request $request, Response $response): void
    {
        $instanceName = preg_replace('/[^a-z0-9\-]/', '', strtolower($request->input('instance_name', '')));
        $displayName = $request->input('display_name', '');
        $userId = $request->input('user_id') ? (int) $request->input('user_id') : null;
        $useDefault = $request->input('use_default_credentials', '1') === '1';

        if (empty($instanceName)) {
            $this->json(['success' => false, 'error' => 'Nome da instância é obrigatório.'], 400);
            return;
        }

        // Buscar credenciais
        if ($useDefault) {
            $default = $this->instanceModel->getDefault();
            $apiUrl = $default ? $default['api_url'] : $this->setting('evolution_api_url', '');
            $apiKey = $default ? $default['api_key'] : $this->setting('evolution_api_key', '');
        } else {
            $apiUrl = $request->input('api_url', '');
            $apiKey = $request->input('api_key', '');
        }

        if (empty($apiUrl) || empty($apiKey)) {
            $this->json(['success' => false, 'error' => 'URL e API Key são obrigatórios.'], 400);
            return;
        }

        // Criar na Evolution API
        $webhookUrl = rtrim($this->setting('site_url', ''), '/') . '/whatsapp/webhook';
        $api = new EvolutionApi($apiUrl, $apiKey, $instanceName);
        $result = $api->createInstance($instanceName, $webhookUrl);

        if (!$result) {
            $this->json(['success' => false, 'error' => 'Erro ao criar instância na Evolution API.'], 500);
            return;
        }

        // Salvar no banco
        $id = $this->instanceModel->create([
            'instance_name' => $instanceName,
            'display_name' => $displayName ?: $instanceName,
            'api_url' => $apiUrl,
            'api_key' => $apiKey,
            'user_id' => $userId,
            'connection_status' => 'close',
        ]);

        $this->json(['success' => true, 'id' => $id]);
    }

    /**
     * Conecta instância (gera QR Code).
     */
    public function connect(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $instance = $this->instanceModel->find($id);
        if (!$instance) {
            $this->json(['success' => false, 'error' => 'Instância não encontrada.'], 404);
            return;
        }

        $api = EvolutionApi::fromInstance($instance);
        $result = $api->connect();

        if (!$result) {
            $this->json(['success' => false, 'error' => 'Não foi possível conectar à Evolution API.'], 500);
            return;
        }

        // Extrair QR Code - a Evolution API pode retornar em diferentes formatos
        $qrCode = $result['base64'] 
            ?? $result['qrcode']['base64'] 
            ?? $result['qrcode'] 
            ?? $result['code'] 
            ?? null;

        // Verificar se já está conectada
        $state = $result['instance']['state'] ?? $result['state'] ?? null;
        if ($state === 'open' || $state === 'connected') {
            $this->instanceModel->updateStatus($id, 'open');
            $this->json(['success' => true, 'connected' => true]);
            return;
        }

        if ($qrCode) {
            // Remover prefixo data:image se já veio com ele (salvar só o base64 puro)
            $qrCodeClean = $qrCode;
            if (str_contains($qrCode, ',')) {
                $qrCodeClean = explode(',', $qrCode, 2)[1] ?? $qrCode;
            }
            $this->instanceModel->updateStatus($id, 'connecting', $qrCodeClean);
            $this->json(['success' => true, 'qrcode' => $qrCodeClean]);
        } else {
            $this->json(['success' => false, 'error' => 'QR Code não retornado pela API. Resposta: ' . json_encode(array_keys($result))], 500);
        }
    }

    /**
     * Verifica status da conexão.
     */
    public function status(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $instance = $this->instanceModel->find($id);
        if (!$instance) {
            $this->json(['success' => false, 'error' => 'Instância não encontrada.'], 404);
            return;
        }

        $api = EvolutionApi::fromInstance($instance);
        $result = $api->connectionState();
        $state = $result['instance']['state'] ?? $result['state'] ?? 'close';

        $this->instanceModel->updateStatus($id, $state);
        $this->json(['success' => true, 'status' => $state]);
    }

    /**
     * Desconecta instância (super_admin only).
     */
    public function disconnect(Request $request, Response $response): void
    {
        if (!WhatsAppMiddleware::isSuperAdmin()) {
            $this->json(['success' => false, 'error' => 'Apenas super admin.'], 403);
            return;
        }

        $id = (int) $request->param('id');
        $instance = $this->instanceModel->find($id);
        if (!$instance) {
            $this->json(['success' => false, 'error' => 'Instância não encontrada.'], 404);
            return;
        }

        $api = EvolutionApi::fromInstance($instance);
        $api->logout();
        $this->instanceModel->updateStatus($id, 'close');
        $this->json(['success' => true]);
    }

    /**
     * Define instância como padrão (super_admin only).
     */
    public function setDefault(Request $request, Response $response): void
    {
        if (!WhatsAppMiddleware::isSuperAdmin()) {
            $this->json(['success' => false, 'error' => 'Apenas super admin.'], 403);
            return;
        }

        $id = (int) $request->param('id');
        $this->instanceModel->setAsDefault($id);
        $this->json(['success' => true]);
    }

    /**
     * Edita instância (super_admin only).
     */
    public function updateInstance(Request $request, Response $response): void
    {
        if (!WhatsAppMiddleware::isSuperAdmin()) {
            $this->json(['success' => false, 'error' => 'Apenas super admin.'], 403);
            return;
        }

        $id = (int) $request->param('id');
        $this->instanceModel->update($id, [
            'display_name' => $request->input('display_name', ''),
            'api_url' => $request->input('api_url', ''),
            'api_key' => $request->input('api_key', ''),
            'user_id' => $request->input('user_id') ? (int) $request->input('user_id') : null,
        ]);
        $this->json(['success' => true]);
    }

    /**
     * Exclui instância (super_admin only).
     */
    public function deleteInstance(Request $request, Response $response): void
    {
        if (!WhatsAppMiddleware::isSuperAdmin()) {
            $this->json(['success' => false, 'error' => 'Apenas super admin.'], 403);
            return;
        }

        $id = (int) $request->param('id');
        $instance = $this->instanceModel->find($id);
        if (!$instance) {
            $this->json(['success' => false, 'error' => 'Instância não encontrada.'], 404);
            return;
        }

        // Deletar na Evolution API
        $api = EvolutionApi::fromInstance($instance);
        $api->deleteInstance();

        // Deletar do banco (cascade)
        $this->instanceModel->delete($id);
        $this->json(['success' => true]);
    }

    // ═══════════════════════════════════════════════
    // CHAT
    // ═══════════════════════════════════════════════

    /**
     * Tela principal do chat.
     */
    public function chat(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $instance = $this->instanceModel->getUserInstance((int) $user['id']);

        if (!$instance) {
            // Tentar qualquer instância conectada como fallback
            $instance = $this->db->fetchOne(
                "SELECT * FROM whatsapp_instances WHERE connection_status = 'open' LIMIT 1"
            );
        }

        if (!$instance) {
            // Mostrar chat vazio com mensagem para configurar
            $this->view('admin/whatsapp/chat', [
                'instance' => null,
                'contactId' => null,
                'labels' => [],
                'teamMembers' => [],
                'currentUser' => $user,
                'noInstance' => true,
                'pageTitle' => 'WhatsApp',
            ], 'admin');
            return;
        }

        $contactId = $request->param('id') ? (int) $request->param('id') : null;
        $labels = $this->labelModel->listAll();

        $teamMembers = $this->db->fetchAll(
            "SELECT id, first_name, last_name FROM users 
             WHERE role IN ('superadmin','admin','attendant','whatsapp_agent','comercial') 
             AND status = 'active' ORDER BY first_name"
        );

        $this->view('admin/whatsapp/chat', [
            'instance' => $instance,
            'contactId' => $contactId,
            'labels' => $labels,
            'teamMembers' => $teamMembers,
            'currentUser' => $user,
            'pageTitle' => 'WhatsApp',
        ], 'admin');
    }

    /**
     * API: Lista contatos (AJAX).
     */
    public function contacts(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $instance = $this->instanceModel->getUserInstance((int) $user['id']);
        if (!$instance) {
            $this->json(['contacts' => [], 'groups' => []]);
            return;
        }

        $filters = [
            'search' => $request->input('search', ''),
            'assigned_to' => $request->input('assigned_to', ''),
            'label_id' => $request->input('label_id', ''),
            'service_status' => $request->input('service_status', ''),
        ];

        $isGroup = $request->input('tab', 'contacts') === 'groups';
        $contacts = $this->contactModel->listFiltered((int) $instance['id'], $filters, $isGroup);

        // Adicionar etiquetas a cada contato
        foreach ($contacts as &$contact) {
            $contact['labels'] = $this->labelModel->getByContact((int) $contact['id']);
            // Badge CRM
            $contact['crm_info'] = $this->db->fetchOne(
                "SELECT b.name as board_name, col.name as column_name 
                 FROM crm_cards cc
                 INNER JOIN crm_columns col ON col.id = cc.column_id
                 INNER JOIN crm_boards b ON b.id = col.board_id
                 WHERE cc.contact_id = ? AND b.is_active = 1 LIMIT 1",
                [(int) $contact['id']]
            );
        }

        $counts = $this->contactModel->countByType((int) $instance['id']);
        $this->json(['contacts' => $contacts, 'counts' => $counts]);
    }

    /**
     * API: Mensagens de um contato (com paginação).
     */
    public function messages(Request $request, Response $response): void
    {
        $contactId = (int) $request->param('id');
        $beforeId = $request->input('before_id') ? (int) $request->input('before_id') : null;

        $messages = $this->messageModel->getByContact($contactId, 50, $beforeId);

        // Zerar não lidas ao abrir chat
        if (!$beforeId) {
            $this->contactModel->markAsRead($contactId);
        }

        $this->json(['messages' => $messages]);
    }

    /**
     * API: Polling de novas mensagens.
     */
    public function poll(Request $request, Response $response): void
    {
        $contactId = (int) $request->param('id');
        $afterId = (int) $request->input('after_id', '0');

        $newMessages = $this->messageModel->getNewMessages($contactId, $afterId);

        // Buscar mensagens deletadas
        $deletedIds = [];
        $contact = $this->contactModel->find($contactId);

        $this->json([
            'messages' => $newMessages,
            'deleted_ids' => $deletedIds,
        ]);
    }

    /**
     * API: Status de ack das mensagens (checks).
     */
    public function messageStatuses(Request $request, Response $response): void
    {
        $contactId = (int) $request->param('id');
        $statuses = $this->messageModel->getMessageStatuses($contactId);
        $this->json(['statuses' => $statuses]);
    }

    /**
     * API: Enviar mensagem de texto.
     */
    public function send(Request $request, Response $response): void
    {
        $contactId = (int) $request->input('contact_id', '0');
        $text = $request->input('message', '');
        $sign = $request->input('sign', '0') === '1';

        if (empty($text) || !$contactId) {
            $this->json(['success' => false, 'error' => 'Mensagem e contato são obrigatórios.'], 400);
            return;
        }

        $contact = $this->contactModel->find($contactId);
        if (!$contact) {
            $this->json(['success' => false, 'error' => 'Contato não encontrado.'], 404);
            return;
        }

        $user = $this->currentUser();
        $instance = $this->instanceModel->find((int) $contact['instance_id']);
        if (!$instance) {
            $this->json(['success' => false, 'error' => 'Instância não encontrada.'], 404);
            return;
        }

        // Assinar mensagem com nome do usuário
        $senderName = null;
        if ($sign) {
            $senderName = $user['first_name'] ?? 'Sistema';
            $text = "*{$senderName}:*\n{$text}";
        }

        // Enviar via Evolution API
        $api = EvolutionApi::fromInstance($instance);
        $result = $api->sendText($contact['remote_jid'], $text);

        if (!$result) {
            $this->json(['success' => false, 'error' => 'Falha ao enviar mensagem.'], 500);
            return;
        }

        // Salvar no banco
        $messageId = $result['key']['id'] ?? ('local_' . uniqid());
        $msgId = $this->db->insert('whatsapp_messages', [
            'instance_id' => (int) $instance['id'],
            'contact_id' => $contactId,
            'remote_jid' => $contact['remote_jid'],
            'message_id' => $messageId,
            'from_me' => 1,
            'message_type' => 'text',
            'message_text' => $text,
            'sender_name' => $senderName,
            'timestamp' => date('Y-m-d H:i:s'),
            'is_read' => 1,
            'ack_status' => 'sent',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Atualizar last_message_at
        $this->contactModel->update($contactId, ['last_message_at' => date('Y-m-d H:i:s')]);

        // Atribuição automática
        if (empty($contact['assigned_to'])) {
            $this->contactModel->update($contactId, ['assigned_to' => (int) $user['id']]);
        }

        $this->json(['success' => true, 'message_id' => $msgId]);
    }

    /**
     * API: Enviar mídia/arquivo.
     */
    public function sendMedia(Request $request, Response $response): void
    {
        $contactId = (int) $request->input('contact_id', '0');
        $caption = $request->input('caption', '');
        $sign = $request->input('sign', '0') === '1';

        $contact = $this->contactModel->find($contactId);
        if (!$contact) {
            $this->json(['success' => false, 'error' => 'Contato não encontrado.'], 404);
            return;
        }

        $instance = $this->instanceModel->find((int) $contact['instance_id']);
        if (!$instance) {
            $this->json(['success' => false, 'error' => 'Instância não encontrada.'], 404);
            return;
        }

        // Upload do arquivo
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['success' => false, 'error' => 'Nenhum arquivo enviado.'], 400);
            return;
        }

        $file = $_FILES['file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $mimeType = $file['type'];

        // Determinar tipo de mídia
        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $videoExts = ['mp4', 'avi', 'mov', '3gp'];
        $audioExts = ['mp3', 'ogg', 'wav', 'aac', 'm4a'];

        if (in_array($ext, $imageExts)) {
            $mediaType = 'image';
            $msgType = 'image';
        } elseif (in_array($ext, $videoExts)) {
            $mediaType = 'video';
            $msgType = 'video';
        } elseif (in_array($ext, $audioExts)) {
            $mediaType = 'audio';
            $msgType = 'audio';
        } else {
            $mediaType = 'document';
            $msgType = 'document';
        }

        // Salvar arquivo localmente
        $month = date('Y-m');
        $uploadDir = BASE_PATH . "/public/uploads/whatsapp_media/{$month}";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $uniqueName = uniqid() . '_' . time() . '.' . $ext;
        $localPath = "{$uploadDir}/{$uniqueName}";
        move_uploaded_file($file['tmp_name'], $localPath);

        $mediaUrl = "/uploads/whatsapp_media/{$month}/{$uniqueName}";
        $publicUrl = rtrim($this->setting('site_url', ''), '/') . $mediaUrl;

        // Assinar caption
        $user = $this->currentUser();
        $senderName = null;
        if ($sign && !empty($caption)) {
            $senderName = $user['first_name'] ?? 'Sistema';
            $caption = "*{$senderName}:*\n{$caption}";
        }

        // Salvar mensagem no banco ANTES (persistência imediata)
        $msgId = $this->db->insert('whatsapp_messages', [
            'instance_id' => (int) $instance['id'],
            'contact_id' => $contactId,
            'remote_jid' => $contact['remote_jid'],
            'message_id' => 'pending_' . uniqid(),
            'from_me' => 1,
            'message_type' => $msgType,
            'message_text' => $caption ?: null,
            'media_url' => $mediaUrl,
            'media_mime_type' => $mimeType,
            'media_filename' => $file['name'],
            'sender_name' => $senderName,
            'timestamp' => date('Y-m-d H:i:s'),
            'is_read' => 1,
            'ack_status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Enviar via Evolution API (tenta URL pública, fallback base64)
        $api = EvolutionApi::fromInstance($instance);

        if ($mediaType === 'audio') {
            $audioBase64 = base64_encode(file_get_contents($localPath));
            $result = $api->sendAudio($contact['remote_jid'], $audioBase64);
        } else {
            $result = $api->sendMedia(
                $contact['remote_jid'],
                $mediaType,
                $publicUrl,
                $caption ?: null,
                $file['name'],
                $mimeType
            );

            // Fallback para base64
            if (!$result) {
                $base64 = base64_encode(file_get_contents($localPath));
                $dataUri = "data:{$mimeType};base64,{$base64}";
                $result = $api->sendMedia(
                    $contact['remote_jid'],
                    $mediaType,
                    $dataUri,
                    $caption ?: null,
                    $file['name'],
                    $mimeType
                );
            }
        }

        // Atualizar status e message_id
        $ackStatus = $result ? 'sent' : 'failed';
        $realMessageId = $result['key']['id'] ?? ('local_' . uniqid());
        $this->db->update('whatsapp_messages', [
            'message_id' => $realMessageId,
            'ack_status' => $ackStatus,
        ], 'id = ?', [$msgId]);

        // Atualizar contato
        $this->contactModel->update($contactId, ['last_message_at' => date('Y-m-d H:i:s')]);
        if (empty($contact['assigned_to'])) {
            $this->contactModel->update($contactId, ['assigned_to' => (int) $user['id']]);
        }

        $this->json(['success' => (bool) $result, 'message_id' => $msgId]);
    }

    // ═══════════════════════════════════════════════
    // CONTATOS
    // ═══════════════════════════════════════════════

    /**
     * API: Detalhes de um contato.
     */
    public function contactDetail(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $contact = $this->contactModel->findWithLabels($id);
        if (!$contact) {
            $this->json(['success' => false, 'error' => 'Contato não encontrado.'], 404);
            return;
        }

        // Briefing
        $contact['briefing'] = $this->db->fetchOne(
            "SELECT * FROM commercial_briefings WHERE contact_id = ?",
            [$id]
        );

        $this->json(['success' => true, 'contact' => $contact]);
    }

    /**
     * API: Atualizar contato (nome, notas, atribuição).
     */
    public function updateContact(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $data = [];

        if ($request->input('contact_name') !== null) $data['contact_name'] = $request->input('contact_name');
        if ($request->input('internal_notes') !== null) $data['internal_notes'] = $request->input('internal_notes');
        if ($request->input('assigned_to') !== null) {
            $data['assigned_to'] = $request->input('assigned_to') ? (int) $request->input('assigned_to') : null;
        }

        if (!empty($data)) {
            $this->contactModel->update($id, $data);
        }

        $this->json(['success' => true]);
    }

    /**
     * API: Alterar status de atendimento.
     */
    public function updateServiceStatus(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $status = $request->input('status', 'novo');
        $validStatuses = ['novo', 'em_atendimento', 'aguardando', 'concluido'];

        if (!in_array($status, $validStatuses)) {
            $this->json(['success' => false, 'error' => 'Status inválido.'], 400);
            return;
        }

        $this->contactModel->update($id, ['service_status' => $status]);
        $this->json(['success' => true]);
    }

    /**
     * API: Toggle etiqueta em contato.
     */
    public function toggleLabel(Request $request, Response $response): void
    {
        $contactId = (int) $request->input('contact_id', '0');
        $labelId = (int) $request->input('label_id', '0');
        $action = $request->input('action', 'add'); // add ou remove

        if ($action === 'remove') {
            $this->labelModel->removeFromContact($contactId, $labelId);
        } else {
            $this->labelModel->addToContact($contactId, $labelId);
        }

        $this->json(['success' => true]);
    }

    /**
     * API: Criar nova etiqueta.
     */
    public function createLabel(Request $request, Response $response): void
    {
        $name = $request->input('name', '');
        $color = $request->input('color', '#6c757d');

        if (empty($name)) {
            $this->json(['success' => false, 'error' => 'Nome é obrigatório.'], 400);
            return;
        }

        $user = $this->currentUser();
        $id = $this->labelModel->create([
            'name' => $name,
            'color' => $color,
            'created_by' => (int) $user['id'],
        ]);

        $this->json(['success' => true, 'id' => $id, 'name' => $name, 'color' => $color]);
    }

    /**
     * API: Iniciar nova conversa.
     */
    public function startConversation(Request $request, Response $response): void
    {
        $phone = preg_replace('/[^0-9]/', '', $request->input('phone', ''));
        $name = $request->input('name', '');

        if (strlen($phone) < 10) {
            $this->json(['success' => false, 'error' => 'Número inválido.'], 400);
            return;
        }

        $user = $this->currentUser();
        $instance = $this->instanceModel->getUserInstance((int) $user['id']);
        if (!$instance) {
            $this->json(['success' => false, 'error' => 'Sem instância disponível.'], 400);
            return;
        }

        $phone = EvolutionApi::normalizePhone($phone);
        $api = EvolutionApi::fromInstance($instance);

        // Verificar se número tem WhatsApp
        $check = $api->checkIsWhatsapp([$phone]);
        if (empty($check) || !isset($check[0]['exists']) || !$check[0]['exists']) {
            $this->json(['success' => false, 'error' => 'Número não encontrado no WhatsApp.'], 400);
            return;
        }

        $jid = $check[0]['jid'] ?? EvolutionApi::phoneToJid($phone);

        // Buscar nome e foto do perfil
        $profileName = $name;
        if (empty($profileName)) {
            $profileName = $check[0]['name'] ?? null;
        }

        $profilePic = null;
        try {
            $picResult = $api->fetchProfilePicture($jid);
            $profilePic = $picResult['profilePictureUrl'] ?? $picResult['picture'] ?? null;
        } catch (\Throwable $e) {}

        // Criar/buscar contato
        $contact = $this->contactModel->findByJid((int) $instance['id'], $jid);
        if (!$contact) {
            $contactId = $this->contactModel->create([
                'instance_id' => (int) $instance['id'],
                'remote_jid' => $jid,
                'phone' => $phone,
                'contact_name' => $profileName,
                'push_name' => $profileName,
                'profile_picture_url' => $profilePic,
                'is_group' => 0,
                'service_status' => 'novo',
                'last_message_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $contactId = (int) $contact['id'];
            // Atualizar foto se não tinha
            if (empty($contact['profile_picture_url']) && $profilePic) {
                $this->contactModel->update($contactId, ['profile_picture_url' => $profilePic]);
            }
        }

        $this->json(['success' => true, 'contact_id' => $contactId]);
    }

    /**
     * API: Excluir contato permanentemente (super_admin only).
     */
    public function deleteContact(Request $request, Response $response): void
    {
        if (!WhatsAppMiddleware::isSuperAdmin()) {
            $this->json(['success' => false, 'error' => 'Apenas super admin.'], 403);
            return;
        }

        $id = (int) $request->param('id');

        // Desvincular cards do CRM
        $this->db->query("UPDATE crm_cards SET contact_id = NULL WHERE contact_id = ?", [$id]);

        // Deletar contato (cascade: mensagens, etiquetas, briefing)
        $this->contactModel->delete($id);
        $this->json(['success' => true]);
    }

    // ═══════════════════════════════════════════════
    // BRIEFING COMERCIAL
    // ═══════════════════════════════════════════════

    /**
     * API: Buscar briefing de um contato.
     */
    public function getBriefing(Request $request, Response $response): void
    {
        $contactId = (int) $request->param('id');
        $briefing = $this->db->fetchOne(
            "SELECT * FROM commercial_briefings WHERE contact_id = ?",
            [$contactId]
        );
        $this->json(['success' => true, 'briefing' => $briefing]);
    }

    /**
     * API: Salvar briefing de um contato.
     */
    public function saveBriefing(Request $request, Response $response): void
    {
        $contactId = (int) $request->param('id');
        $user = $this->currentUser();

        $data = [
            'contact_id' => $contactId,
            'need' => $request->input('need', ''),
            'main_pain' => $request->input('main_pain', ''),
            'current_solution' => $request->input('current_solution', ''),
            'expected_goal' => $request->input('expected_goal', ''),
            'urgency' => $request->input('urgency', ''),
            'investment_range' => $request->input('investment_range', ''),
            'decision_level' => $request->input('decision_level', ''),
            'lead_temperature' => $request->input('lead_temperature') ?: null,
            'main_objection' => $request->input('main_objection', ''),
            'next_step' => $request->input('next_step', ''),
            'next_contact_date' => $request->input('next_contact_date') ?: null,
            'notes' => $request->input('notes', ''),
            'created_by' => (int) $user['id'],
        ];

        $existing = $this->db->fetchOne(
            "SELECT id FROM commercial_briefings WHERE contact_id = ?",
            [$contactId]
        );

        if ($existing) {
            unset($data['contact_id'], $data['created_by']);
            $this->db->update('commercial_briefings', $data, 'id = ?', [(int) $existing['id']]);
        } else {
            $this->db->insert('commercial_briefings', $data);
        }

        // Sincronizar investment_range com card do CRM
        if (!empty($data['investment_range'])) {
            $value = (float) preg_replace('/[^0-9.,]/', '', str_replace(',', '.', $data['investment_range']));
            $this->db->query(
                "UPDATE crm_cards SET value = ? WHERE contact_id = ?",
                [$value, $contactId]
            );
        }

        $this->json(['success' => true]);
    }

    // ═══════════════════════════════════════════════
    // CRM INTEGRATION (do chat)
    // ═══════════════════════════════════════════════

    /**
     * API: Adicionar contato ao CRM (criar card).
     */
    public function addToCrm(Request $request, Response $response): void
    {
        $contactId = (int) $request->input('contact_id', '0');
        $columnId = (int) $request->input('column_id', '0');

        $contact = $this->contactModel->find($contactId);
        if (!$contact || !$columnId) {
            $this->json(['success' => false, 'error' => 'Dados inválidos.'], 400);
            return;
        }

        $user = $this->currentUser();
        $briefing = $this->db->fetchOne(
            "SELECT investment_range, lead_temperature FROM commercial_briefings WHERE contact_id = ?",
            [$contactId]
        );

        $value = null;
        if ($briefing && !empty($briefing['investment_range'])) {
            $value = (float) preg_replace('/[^0-9.,]/', '', str_replace(',', '.', $briefing['investment_range']));
        }

        $cardId = $this->db->insert('crm_cards', [
            'column_id' => $columnId,
            'contact_id' => $contactId,
            'title' => $contact['contact_name'] ?? $contact['push_name'] ?? $contact['phone'] ?? 'Sem nome',
            'phone' => $contact['phone'],
            'value' => $value,
            'lead_outcome' => 'open',
            'assigned_to' => $contact['assigned_to'],
            'created_by' => (int) $user['id'],
            'position' => 0,
        ]);

        // Registrar atividade
        $this->db->insert('crm_card_activities', [
            'card_id' => $cardId,
            'user_id' => (int) $user['id'],
            'activity_type' => 'create',
            'description' => 'Card criado a partir do chat WhatsApp',
        ]);

        $this->json(['success' => true, 'card_id' => $cardId]);
    }

    // ═══════════════════════════════════════════════
    // RESPOSTAS RÁPIDAS
    // ═══════════════════════════════════════════════

    /**
     * API: Listar respostas rápidas.
     */
    public function quickReplies(Request $request, Response $response): void
    {
        $replies = $this->db->fetchAll(
            "SELECT * FROM whatsapp_quick_replies ORDER BY shortcut ASC"
        );
        $this->json(['replies' => $replies]);
    }

    /**
     * API: Criar/editar resposta rápida.
     */
    public function saveQuickReply(Request $request, Response $response): void
    {
        $id = $request->input('id') ? (int) $request->input('id') : null;
        $shortcut = strtolower(preg_replace('/[^a-z0-9]/', '', $request->input('shortcut', '')));
        $message = $request->input('message', '');

        if (empty($shortcut)) {
            $this->json(['success' => false, 'error' => 'Atalho é obrigatório.'], 400);
            return;
        }

        $data = ['shortcut' => $shortcut, 'message' => $message];

        // Upload de anexo
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['attachment'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $uploadDir = BASE_PATH . '/public/uploads/quick_replies';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $uniqueName = uniqid() . '.' . $ext;
            move_uploaded_file($file['tmp_name'], "{$uploadDir}/{$uniqueName}");

            $data['attachment_path'] = "/uploads/quick_replies/{$uniqueName}";
            $data['attachment_name'] = $file['name'];
            $data['attachment_mime'] = $file['type'];
        }

        if ($id) {
            $this->db->update('whatsapp_quick_replies', $data, 'id = ?', [$id]);
        } else {
            $user = $this->currentUser();
            $data['created_by'] = (int) $user['id'];
            $id = $this->db->insert('whatsapp_quick_replies', $data);
        }

        $this->json(['success' => true, 'id' => $id]);
    }

    /**
     * API: Excluir resposta rápida.
     */
    public function deleteQuickReply(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $this->db->delete('whatsapp_quick_replies', 'id = ?', [$id]);
        $this->json(['success' => true]);
    }

    /**
     * API: Enviar resposta rápida com anexo.
     */
    public function sendQuickReply(Request $request, Response $response): void
    {
        $contactId = (int) $request->input('contact_id', '0');
        $replyId = (int) $request->input('reply_id', '0');
        $sign = $request->input('sign', '0') === '1';

        $reply = $this->db->fetchOne("SELECT * FROM whatsapp_quick_replies WHERE id = ?", [$replyId]);
        if (!$reply) {
            $this->json(['success' => false, 'error' => 'Resposta rápida não encontrada.'], 404);
            return;
        }

        $contact = $this->contactModel->find($contactId);
        if (!$contact) {
            $this->json(['success' => false, 'error' => 'Contato não encontrado.'], 404);
            return;
        }

        $instance = $this->instanceModel->find((int) $contact['instance_id']);
        $api = EvolutionApi::fromInstance($instance);
        $user = $this->currentUser();

        $message = $reply['message'] ?? '';
        $senderName = null;
        if ($sign && !empty($message)) {
            $senderName = $user['first_name'] ?? 'Sistema';
            $message = "*{$senderName}:*\n{$message}";
        }

        // Se tem anexo, envia como mídia
        if (!empty($reply['attachment_path'])) {
            $publicUrl = rtrim($this->setting('site_url', ''), '/') . $reply['attachment_path'];
            $mime = $reply['attachment_mime'] ?? 'application/octet-stream';
            $mediaType = str_starts_with($mime, 'image') ? 'image' : 
                        (str_starts_with($mime, 'video') ? 'video' : 'document');

            $result = $api->sendMedia(
                $contact['remote_jid'], $mediaType, $publicUrl,
                $message ?: null, $reply['attachment_name'], $mime
            );
        } else {
            $result = $api->sendText($contact['remote_jid'], $message);
        }

        if ($result) {
            $messageId = $result['key']['id'] ?? ('local_' . uniqid());
            $this->db->insert('whatsapp_messages', [
                'instance_id' => (int) $instance['id'],
                'contact_id' => $contactId,
                'remote_jid' => $contact['remote_jid'],
                'message_id' => $messageId,
                'from_me' => 1,
                'message_type' => !empty($reply['attachment_path']) ? 'document' : 'text',
                'message_text' => $message,
                'media_url' => $reply['attachment_path'] ?? null,
                'media_filename' => $reply['attachment_name'] ?? null,
                'sender_name' => $senderName,
                'timestamp' => date('Y-m-d H:i:s'),
                'is_read' => 1,
                'ack_status' => 'sent',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $this->contactModel->update($contactId, ['last_message_at' => date('Y-m-d H:i:s')]);
            if (empty($contact['assigned_to'])) {
                $this->contactModel->update($contactId, ['assigned_to' => (int) $user['id']]);
            }
        }

        $this->json(['success' => (bool) $result]);
    }

    // ═══════════════════════════════════════════════
    // TRANSCRIÇÃO DE ÁUDIO
    // ═══════════════════════════════════════════════

    /**
     * API: Transcrever áudio (OpenAI Whisper).
     */
    public function transcribeAudio(Request $request, Response $response): void
    {
        $msgId = (int) $request->param('id');
        $msg = $this->messageModel->find($msgId);

        if (!$msg || $msg['message_type'] !== 'audio' || empty($msg['media_url'])) {
            $this->json(['success' => false, 'error' => 'Mensagem de áudio não encontrada.'], 404);
            return;
        }

        // Cache
        if (!empty($msg['transcription'])) {
            $this->json(['success' => true, 'transcription' => $msg['transcription']]);
            return;
        }

        $apiKey = $this->setting('openai_api_key', '');
        if (empty($apiKey)) {
            $this->json(['success' => false, 'error' => 'API Key OpenAI não configurada.'], 400);
            return;
        }

        $filePath = BASE_PATH . '/public' . $msg['media_url'];
        if (!file_exists($filePath)) {
            $this->json(['success' => false, 'error' => 'Arquivo de áudio não encontrado.'], 404);
            return;
        }

        // Enviar para OpenAI Whisper
        $ch = curl_init('https://api.openai.com/v1/audio/transcriptions');
        $cFile = new \CURLFile($filePath);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apiKey,
            ],
            CURLOPT_POSTFIELDS => [
                'file' => $cFile,
                'model' => 'whisper-1',
                'language' => 'pt',
            ],
            CURLOPT_TIMEOUT => 60,
        ]);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            $this->json(['success' => false, 'error' => 'Erro na transcrição.'], 500);
            return;
        }

        $data = json_decode($result, true);
        $transcription = $data['text'] ?? '';

        // Salvar no banco
        $this->messageModel->saveTranscription($msgId, $transcription);

        $this->json(['success' => true, 'transcription' => $transcription]);
    }

    // ═══════════════════════════════════════════════
    // SINCRONIZAÇÃO
    // ═══════════════════════════════════════════════

    /**
     * API: Sincronizar grupos.
     */
    public function syncGroups(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $instance = $this->instanceModel->getUserInstance((int) $user['id']);
        if (!$instance) {
            $this->json(['success' => false, 'error' => 'Sem instância.'], 400);
            return;
        }

        $api = EvolutionApi::fromInstance($instance);
        $groups = $api->fetchAllGroups();

        if (!$groups) {
            $this->json(['success' => false, 'error' => 'Erro ao buscar grupos.'], 500);
            return;
        }

        $updated = 0;
        foreach ($groups as $group) {
            $jid = $group['id'] ?? $group['jid'] ?? '';
            $subject = $group['subject'] ?? $group['name'] ?? '';
            if (empty($jid)) continue;

            $existing = $this->contactModel->findByJid((int) $instance['id'], $jid);
            if ($existing) {
                $this->contactModel->update((int) $existing['id'], ['contact_name' => $subject]);
            } else {
                $this->contactModel->create([
                    'instance_id' => (int) $instance['id'],
                    'remote_jid' => $jid,
                    'contact_name' => $subject,
                    'is_group' => 1,
                    'service_status' => 'novo',
                ]);
            }
            $updated++;
        }

        $this->json(['success' => true, 'updated' => $updated]);
    }

    /**
     * API: Sincronizar fotos de perfil.
     */
    public function syncPhotos(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $instance = $this->instanceModel->getUserInstance((int) $user['id']);
        if (!$instance) {
            $this->json(['success' => false, 'error' => 'Sem instância.'], 400);
            return;
        }

        $contacts = $this->db->fetchAll(
            "SELECT id, remote_jid FROM whatsapp_contacts 
             WHERE instance_id = ? AND profile_picture_url IS NULL LIMIT 100",
            [(int) $instance['id']]
        );

        $api = EvolutionApi::fromInstance($instance);
        $updated = 0;

        foreach ($contacts as $contact) {
            try {
                $result = $api->fetchProfilePicture($contact['remote_jid']);
                $url = $result['profilePictureUrl'] ?? $result['picture'] ?? null;
                if ($url) {
                    $this->contactModel->update((int) $contact['id'], ['profile_picture_url' => $url]);
                    $updated++;
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        $this->json(['success' => true, 'updated' => $updated]);
    }

    /**
     * API: Re-registrar webhook da instância.
     */
    public function registerWebhook(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $instance = $this->instanceModel->find($id);
        if (!$instance) {
            $this->json(['success' => false, 'error' => 'Instância não encontrada.'], 404);
            return;
        }

        $webhookUrl = rtrim($this->setting('site_url', ''), '/') . '/whatsapp/webhook';
        $api = EvolutionApi::fromInstance($instance);
        $result = $api->setWebhook($webhookUrl);

        $this->json(['success' => (bool) $result]);
    }

    // ═══════════════════════════════════════════════
    // WEBHOOK (público, sem autenticação)
    // ═══════════════════════════════════════════════

    /**
     * Webhook da Evolution API — recebe eventos em tempo real.
     */
    public function webhook(Request $request, Response $response): void
    {
        $payload = json_decode(file_get_contents('php://input'), true);
        if (!$payload) {
            $this->json(['status' => 'ok']);
            return;
        }

        $event = $payload['event'] ?? '';
        $instanceName = $payload['instance'] ?? '';

        // Buscar instância
        $instance = $this->db->fetchOne(
            "SELECT * FROM whatsapp_instances WHERE instance_name = ? LIMIT 1",
            [$instanceName]
        );

        if (!$instance) {
            $this->json(['status' => 'instance_not_found']);
            return;
        }

        switch ($event) {
            case 'messages.upsert':
                $this->handleMessageUpsert($instance, $payload['data'] ?? []);
                break;
            case 'messages.update':
                $this->handleMessageUpdate($instance, $payload['data'] ?? []);
                break;
            case 'messages.delete':
                $this->handleMessageDelete($instance, $payload['data'] ?? []);
                break;
            case 'connection.update':
                $this->handleConnectionUpdate($instance, $payload['data'] ?? []);
                break;
            case 'qrcode.updated':
                $this->handleQrCodeUpdate($instance, $payload['data'] ?? []);
                break;
        }

        $this->json(['status' => 'ok']);
    }

    // ═══════════════════════════════════════════════
    // WEBHOOK HANDLERS (privado)
    // ═══════════════════════════════════════════════

    private function handleMessageUpsert(array $instance, array $data): void
    {
        $key = $data['key'] ?? [];
        $remoteJid = $key['remoteJid'] ?? '';
        $fromMe = $key['fromMe'] ?? false;
        $messageId = $key['id'] ?? '';

        // Ignorar: status, broadcast, fromMe, distribuição
        if (empty($remoteJid) || str_contains($remoteJid, 'status@') || str_contains($remoteJid, 'broadcast')) {
            return;
        }
        if ($fromMe) return; // Mensagens próprias já são salvas no envio

        // Deduplicação
        $existing = $this->messageModel->findByMessageId((int) $instance['id'], $messageId);
        if ($existing) return;

        $instanceId = (int) $instance['id'];
        $pushName = $data['pushName'] ?? '';
        $timestamp = $data['messageTimestamp'] ?? time();
        $msgContent = $data['message'] ?? [];

        // Detectar tipo de mensagem
        [$msgType, $msgText, $mediaData] = $this->parseMessageContent($msgContent);

        // Verificar se é reação
        if (isset($msgContent['reactionMessage'])) {
            $msgType = 'reaction';
            $msgText = $msgContent['reactionMessage']['text'] ?? '';
            if (empty($msgText)) return; // Reação removida
        }

        // Mensagem apagada (protocol REVOKE)
        if (isset($msgContent['protocolMessage']['type']) && $msgContent['protocolMessage']['type'] === 'REVOKE') {
            $revokedId = $msgContent['protocolMessage']['key']['id'] ?? '';
            if ($revokedId) {
                $this->messageModel->markDeleted($instanceId, $revokedId);
            }
            return;
        }

        // Identificar se é grupo
        $isGroup = str_ends_with($remoteJid, '@g.us');
        $phone = $isGroup ? '' : EvolutionApi::jidToPhone($remoteJid);
        $participantJid = $key['participant'] ?? $data['participant'] ?? null;

        // Upsert contato
        $contact = $this->upsertWebhookContact($instanceId, $remoteJid, $phone, $pushName, $isGroup);
        $contactId = (int) $contact['id'];

        // Download mídia se necessário
        $mediaUrl = null;
        if ($mediaData) {
            if (!empty($mediaData['base64'])) {
                $mediaUrl = $this->saveMediaFromBase64($mediaData);
            } else {
                // Fallback: tentar buscar mídia via API getBase64FromMediaMessage
                try {
                    $api = EvolutionApi::fromInstance($instance);
                    $mediaResult = $api->getBase64FromMedia([
                        'key' => $key,
                        'message' => $msgContent,
                    ]);
                    if ($mediaResult && !empty($mediaResult['base64'])) {
                        $mediaData['base64'] = $mediaResult['base64'];
                        $mediaUrl = $this->saveMediaFromBase64($mediaData);
                    }
                } catch (\Throwable $e) {
                    // Mídia não disponível — mensagem será salva sem ela
                }
            }
        }

        // Salvar mensagem
        $this->db->insert('whatsapp_messages', [
            'instance_id' => $instanceId,
            'contact_id' => $contactId,
            'remote_jid' => $remoteJid,
            'message_id' => $messageId,
            'from_me' => 0,
            'message_type' => $msgType,
            'message_text' => $msgText,
            'media_url' => $mediaUrl,
            'media_mime_type' => $mediaData['mimetype'] ?? null,
            'media_filename' => $mediaData['filename'] ?? null,
            'sender_name' => $pushName ?: null,
            'participant_jid' => $participantJid,
            'timestamp' => date('Y-m-d H:i:s', (int) $timestamp),
            'is_read' => 0,
            'ack_status' => null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Incrementar não lidas
        $this->contactModel->incrementUnread($contactId);

        // Atualizar last_message_at
        $this->contactModel->update($contactId, [
            'last_message_at' => date('Y-m-d H:i:s', (int) $timestamp),
        ]);

        // Regra automática: se concluído, volta para "novo"
        if ($contact['service_status'] === 'concluido') {
            $this->contactModel->update($contactId, ['service_status' => 'novo']);
        }
    }

    private function handleMessageUpdate(array $instance, array $data): void
    {
        // Pode ser array de updates
        $updates = isset($data[0]) ? $data : [$data];
        foreach ($updates as $update) {
            $messageId = $update['key']['id'] ?? '';
            if (empty($messageId)) continue;

            // Verificar deleção
            if (isset($update['update']['message']) && $update['update']['message'] === null) {
                $this->messageModel->markDeleted((int) $instance['id'], $messageId);
                continue;
            }

            // ACK update
            $ack = $update['update']['status'] ?? $update['status'] ?? null;
            if ($ack !== null) {
                $ackMap = [1 => 'sent', 2 => 'delivered', 3 => 'read', 4 => 'read'];
                $ackStatus = $ackMap[$ack] ?? 'sent';
                $this->messageModel->updateAck((int) $instance['id'], $messageId, $ackStatus);
            }
        }
    }

    private function handleMessageDelete(array $instance, array $data): void
    {
        $messageId = $data['key']['id'] ?? $data['id'] ?? '';
        if ($messageId) {
            $this->messageModel->markDeleted((int) $instance['id'], $messageId);
        }
    }

    private function handleConnectionUpdate(array $instance, array $data): void
    {
        $state = $data['state'] ?? $data['instance']['state'] ?? 'close';
        $this->instanceModel->updateStatus((int) $instance['id'], $state);
    }

    private function handleQrCodeUpdate(array $instance, array $data): void
    {
        $qrCode = $data['qrcode']['base64'] ?? $data['base64'] ?? null;
        $this->instanceModel->updateStatus((int) $instance['id'], 'connecting', $qrCode);
    }

    // ═══════════════════════════════════════════════
    // HELPERS PRIVADOS
    // ═══════════════════════════════════════════════

    /**
     * Parse do conteúdo da mensagem recebida via webhook.
     * Retorna [tipo, texto, mediaData].
     */
    private function parseMessageContent(array $msg): array
    {
        if (isset($msg['conversation'])) {
            return ['text', $msg['conversation'], null];
        }
        if (isset($msg['extendedTextMessage'])) {
            return ['text', $msg['extendedTextMessage']['text'] ?? '', null];
        }
        if (isset($msg['imageMessage'])) {
            return ['image', $msg['imageMessage']['caption'] ?? '', [
                'base64' => $msg['imageMessage']['base64'] ?? null,
                'mimetype' => $msg['imageMessage']['mimetype'] ?? 'image/jpeg',
                'filename' => null,
            ]];
        }
        if (isset($msg['audioMessage'])) {
            return ['audio', null, [
                'base64' => $msg['audioMessage']['base64'] ?? null,
                'mimetype' => $msg['audioMessage']['mimetype'] ?? 'audio/ogg',
                'filename' => null,
            ]];
        }
        if (isset($msg['videoMessage'])) {
            return ['video', $msg['videoMessage']['caption'] ?? '', [
                'base64' => $msg['videoMessage']['base64'] ?? null,
                'mimetype' => $msg['videoMessage']['mimetype'] ?? 'video/mp4',
                'filename' => null,
            ]];
        }
        if (isset($msg['documentMessage'])) {
            return ['document', $msg['documentMessage']['caption'] ?? '', [
                'base64' => $msg['documentMessage']['base64'] ?? null,
                'mimetype' => $msg['documentMessage']['mimetype'] ?? 'application/octet-stream',
                'filename' => $msg['documentMessage']['fileName'] ?? 'documento',
            ]];
        }
        if (isset($msg['stickerMessage'])) {
            return ['sticker', null, [
                'base64' => $msg['stickerMessage']['base64'] ?? null,
                'mimetype' => 'image/webp',
                'filename' => null,
            ]];
        }
        if (isset($msg['locationMessage'])) {
            $lat = $msg['locationMessage']['degreesLatitude'] ?? '';
            $lng = $msg['locationMessage']['degreesLongitude'] ?? '';
            return ['location', "📍 {$lat}, {$lng}", null];
        }

        return ['unknown', null, null];
    }

    /**
     * Salva mídia base64 em arquivo local.
     */
    private function saveMediaFromBase64(array $mediaData): ?string
    {
        $base64 = $mediaData['base64'] ?? '';
        if (empty($base64)) return null;

        $mime = $mediaData['mimetype'] ?? 'application/octet-stream';
        $extMap = [
            'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp',
            'image/gif' => 'gif', 'audio/ogg' => 'ogg', 'audio/mpeg' => 'mp3',
            'audio/mp4' => 'm4a', 'video/mp4' => 'mp4', 'application/pdf' => 'pdf',
        ];
        $ext = $extMap[$mime] ?? 'bin';

        $month = date('Y-m');
        $uploadDir = BASE_PATH . "/public/uploads/whatsapp_media/{$month}";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $fileName = uniqid() . '_' . time() . '.' . $ext;
        $filePath = "{$uploadDir}/{$fileName}";
        file_put_contents($filePath, base64_decode($base64));

        return "/uploads/whatsapp_media/{$month}/{$fileName}";
    }

    /**
     * Upsert de contato via webhook (com deduplicação).
     */
    private function upsertWebhookContact(int $instanceId, string $jid, string $phone, string $pushName, bool $isGroup): array
    {
        // 1. Buscar por JID exato
        $contact = $this->contactModel->findByJid($instanceId, $jid);
        if ($contact) {
            // Atualizar push_name (nunca sobrescreve contact_name)
            $updateData = ['push_name' => $pushName ?: $contact['push_name']];
            if (!empty($phone)) $updateData['phone'] = $phone;
            $this->db->update('whatsapp_contacts', $updateData, 'id = ?', [(int) $contact['id']]);
            return $contact;
        }

        // 2. Buscar por últimos 8 dígitos (deduplicação 9° dígito)
        if (!$isGroup && strlen($phone) >= 8) {
            $last8 = substr($phone, -8);
            $contact = $this->db->fetchOne(
                "SELECT * FROM whatsapp_contacts 
                 WHERE instance_id = ? AND is_group = 0 AND RIGHT(phone, 8) = ? LIMIT 1",
                [$instanceId, $last8]
            );
            if ($contact) {
                $this->db->update('whatsapp_contacts', [
                    'remote_jid' => $jid,
                    'phone' => $phone,
                    'push_name' => $pushName ?: $contact['push_name'],
                ], 'id = ?', [(int) $contact['id']]);
                return $contact;
            }
        }

        // 3. Criar novo
        $contactId = $this->contactModel->create([
            'instance_id' => $instanceId,
            'remote_jid' => $jid,
            'phone' => $phone,
            'contact_name' => $pushName ?: null,
            'push_name' => $pushName ?: null,
            'is_group' => $isGroup ? 1 : 0,
            'service_status' => 'novo',
            'last_message_at' => date('Y-m-d H:i:s'),
            'unread_count' => 0,
        ]);

        return $this->contactModel->find($contactId);
    }
}
