<?php
declare(strict_types=1);

namespace App\Controllers\Frontend;

use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Database;

class PageController extends Controller
{
    public function about(Request $request, Response $response): void
    {
        $this->view('frontend/pages/about', [
            'pageTitle' => 'Sobre Nós - Punta Cana para Brasileiros',
            'metaDescription' => 'Conheça o casal apaixonado por Punta Cana que dedica sua vida a criar experiências únicas e memoráveis para brasileiros.',
        ], 'app');
    }

    public function contact(Request $request, Response $response): void
    {
        $this->view('frontend/pages/contact', [
            'pageTitle' => 'Contato - Punta Cana para Brasileiros',
            'metaDescription' => 'Entre em contato com nossa equipe de especialistas brasileiros em Punta Cana.',
        ], 'app');
    }

    public function sendContact(Request $request, Response $response): void
    {
        $name = trim($request->input('name', ''));
        $email = trim($request->input('email', ''));
        $subject = trim($request->input('subject', ''));
        $message = trim($request->input('message', ''));

        if (!$name || !$email || !$message) {
            $this->flash('error', 'Preencha todos os campos obrigatórios.');
            $this->redirect('/contato');
            return;
        }

        // Enviar email para o admin
        $emailService = new \App\Services\EmailService();
        $adminEmail = $this->setting('admin_email', 'contato@puntacanaparabrasileiros.com');

        $body = "<h3>Nova mensagem de contato</h3>
                 <p><strong>Nome:</strong> {$name}</p>
                 <p><strong>Email:</strong> {$email}</p>
                 <p><strong>Assunto:</strong> {$subject}</p>
                 <p><strong>Mensagem:</strong></p>
                 <p>{$message}</p>";

        $emailService->send($adminEmail, 'Admin', 'Contato: ' . ($subject ?: 'Nova mensagem'), $body);

        // Log
        $this->db->insert('activity_log', [
            'action' => 'contact_form',
            'entity_type' => 'contact',
            'details' => json_encode(['name' => $name, 'email' => $email, 'subject' => $subject]),
            'ip_address' => $request->ip(),
        ]);

        $this->flash('success', 'Mensagem enviada com sucesso! Retornaremos em breve.');
        $this->redirect('/contato');
    }

    public function affiliateTerms(Request $request, Response $response): void
    {
        $this->view('frontend/pages/affiliate-terms', [
            'pageTitle' => 'Termos e Condições do Programa de Afiliados',
            'metaDescription' => 'Termos e condições do programa de afiliados da Punta Cana para Brasileiros.',
        ], 'app');
    }

    public function cancellationPolicy(Request $request, Response $response): void
    {
        $this->view('frontend/pages/cancellation-policy', [
            'pageTitle' => 'Políticas de Cancelamento - Punta Cana para Brasileiros',
            'metaDescription' => 'Política de cancelamento e reembolso da Punta Cana para Brasileiros.',
        ], 'app');
    }

    public function privacyPolicy(Request $request, Response $response): void
    {
        $this->view('frontend/pages/privacy-policy', [
            'pageTitle' => 'Políticas de Privacidade - Punta Cana para Brasileiros',
            'metaDescription' => 'Política de privacidade e proteção de dados da Punta Cana para Brasileiros.',
        ], 'app');
    }

    public function affiliateProgram(Request $request, Response $response): void
    {
        $this->view('frontend/pages/affiliate-program', [
            'pageTitle' => 'Programa de Afiliados - Punta Cana para Brasileiros',
            'metaDescription' => 'Ganhe comissões divulgando experiências turísticas incríveis em Punta Cana. Junte-se ao nosso programa de afiliados.',
        ], 'app');
    }

    public function affiliateRegister(Request $request, Response $response): void
    {
        $this->view('frontend/pages/affiliate-register', [
            'pageTitle' => 'Cadastre-se como Afiliado - Punta Cana para Brasileiros',
            'metaDescription' => 'Cadastre-se no programa de afiliados e comece a ganhar comissões.',
        ], 'app');
    }

    public function affiliateLogin(Request $request, Response $response): void
    {
        $this->view('frontend/pages/affiliate-login', [
            'pageTitle' => 'Login Afiliado - Punta Cana para Brasileiros',
            'metaDescription' => 'Acesse sua conta de afiliado para gerenciar links e acompanhar comissões.',
        ], 'app');
    }

    public function affiliateRegisterStore(Request $request, Response $response): void
    {
        $data = $request->only([
            'username', 'first_name', 'last_name', 'phone', 'email', 'password',
            'password_confirmation', 'payment_email', 'pix', 'website',
            'followers_count', 'niche', 'content_type', 'promotion_strategy',
        ]);

        // Validação básica
        $errors = [];
        if (empty($data['username'])) $errors['username'] = 'Nome de usuário é obrigatório.';
        if (empty($data['first_name'])) $errors['first_name'] = 'Nome é obrigatório.';
        if (empty($data['last_name'])) $errors['last_name'] = 'Sobrenome é obrigatório.';
        if (empty($data['phone'])) $errors['phone'] = 'WhatsApp/Telefone é obrigatório.';
        if (!filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email inválido.';
        if (strlen($data['password'] ?? '') < 6) $errors['password'] = 'Senha deve ter pelo menos 6 caracteres.';
        if (($data['password'] ?? '') !== ($data['password_confirmation'] ?? '')) $errors['password_confirmation'] = 'Senhas não coincidem.';
        if (empty($data['pix'])) $errors['pix'] = 'PIX é obrigatório.';
        if (empty($data['followers_count'])) $errors['followers_count'] = 'Quantidade de seguidores é obrigatória.';
        if (empty($data['niche'])) $errors['niche'] = 'Nicho é obrigatório.';
        if (empty($data['content_type'])) $errors['content_type'] = 'Tipo de conteúdo é obrigatório.';

        // Verificar email duplicado
        $userModel = new \App\Models\User();
        if (empty($errors['email']) && $userModel->findByEmail($data['email'])) {
            $errors['email'] = 'Este email já está cadastrado.';
        }

        if (!empty($errors)) {
            $this->flash('errors', $errors);
            $this->flash('old', $data);
            $this->redirect('/cadastro-afiliado');
            return;
        }

        // Criar usuário com role affiliate
        $userId = $userModel->createUser([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => strtolower(trim($data['email'])),
            'password' => $data['password'],
            'phone' => $data['phone'],
            'role' => 'customer',
            'status' => 'active',
            'email_verified_at' => date('Y-m-d H:i:s'),
        ]);

        // Criar registro de afiliado (pendente aprovação)
        $affiliateModel = new \App\Models\Affiliate();
        $affiliateModel->create([
            'user_id' => $userId,
            'status' => 'pending',
            'commission_rate' => 20.00,
            'cookie_days' => 30,
            'payment_email' => $data['payment_email'] ?? $data['email'],
            'notes' => json_encode([
                'username' => $data['username'],
                'pix' => $data['pix'],
                'website' => $data['website'],
                'followers_count' => $data['followers_count'],
                'niche' => $data['niche'],
                'content_type' => $data['content_type'],
                'promotion_strategy' => $data['promotion_strategy'],
            ]),
        ]);

        // Notificar admin
        $emailService = new \App\Services\EmailService();
        $adminEmail = $this->setting('admin_email', '');
        if ($adminEmail) {
            $emailService->send($adminEmail, 'Admin', 'Novo Cadastro de Afiliado: ' . $data['first_name'] . ' ' . $data['last_name'], '<p>Um novo afiliado se cadastrou e aguarda aprovação.</p><p>Email: ' . e($data['email']) . '</p><p>Instagram/Site: ' . e($data['website'] ?? '') . '</p>');
        }

        $this->flash('success', 'Cadastro realizado com sucesso! Nossa equipe analisará seu perfil e entrará em contato em até 48 horas.');
        $this->redirect('/cadastro-afiliado');
    }

    public function terms(Request $request, Response $response): void
    {
        $this->view('frontend/pages/terms', [
            'pageTitle' => 'Termos e Condições - Punta Cana para Brasileiros',
            'metaDescription' => 'Termos e condições de uso do site Punta Cana para Brasileiros.',
        ], 'app');
    }

    public function search(Request $request, Response $response): void
    {
        $query = trim($request->query('q', ''));
        $results = ['trips' => [], 'blog' => []];

        if ($query !== '') {
            // Buscar passeios
            $tripModel = new \App\Models\Trip();
            $packageModel = new \App\Models\TripPackage();
            $tripResults = $tripModel->search($query, 1, 6);
            foreach ($tripResults['items'] as &$trip) {
                $packages = $packageModel->getByTrip((int) $trip['id']);
                $trip['min_price'] = 0;
                if (!empty($packages)) {
                    $trip['min_price'] = $packageModel->getBasePrice((int) $packages[0]['id']);
                }
            }
            $results['trips'] = $tripResults['items'];

            // Buscar blog
            $blogResults = $this->db->fetchAll(
                "SELECT * FROM blog_posts WHERE status = 'published' AND (title LIKE ? OR content LIKE ?) ORDER BY published_at DESC LIMIT 4",
                ['%' . $query . '%', '%' . $query . '%']
            );
            $results['blog'] = $blogResults;
        }

        $this->view('frontend/pages/search', [
            'query' => $query,
            'results' => $results,
            'pageTitle' => $query ? 'Resultados para "' . $query . '"' : 'Pesquisar',
        ], 'app');
    }

    public function show(Request $request, Response $response): void
    {
        $slug = $request->param('slug', '');

        $page = $this->db->fetchOne(
            "SELECT * FROM pages WHERE slug = ? AND status = 'published'",
            [$slug]
        );

        if (!$page) {
            $this->abort(404, 'Página não encontrada.');
        }

        $this->view('frontend/pages/show', [
            'page' => $page,
            'pageTitle' => $page['meta_title'] ?: $page['title'],
            'metaDescription' => $page['meta_description'] ?? '',
        ], 'app');
    }
}
