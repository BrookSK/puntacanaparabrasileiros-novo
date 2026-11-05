<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\Setting;

/**
 * Políticas comerciais e câmbio, editáveis pela equipe (gestores) sem depender
 * do superadmin/equipe técnica.
 */
class PoliciesController extends Controller
{
    /** Textos da página do passeio (accordions). */
    public const DEFAULT_PAYMENT_TEXT = "Reserve com **{sinal}% de sinal via Pix, em reais**, e envie o comprovante. Pague os {restante}% restantes no primeiro contato presencial com nossa equipe no destino.\n\nA reserva é confirmada após o sinal e o envio do comprovante. Em caso de dúvida, fale com nossa equipe pelo WhatsApp.";

    public const DEFAULT_CANCELLATION_SUMMARY = "- **100%** de reembolso cancelando com **mais de 48h** de antecedência.\n- **50%** de reembolso cancelando **entre 48h e 24h** antes do passeio.\n- **0%** com **menos de 24h** ou não comparecimento (no show).\n- Troca de data gratuita com pelo menos 24h de antecedência, sujeita à disponibilidade.\n- Se cancelarmos por clima, operação ou segurança, você escolhe: reagendar ou reembolso total.\n- Reembolso pelo mesmo método de pagamento, em 3 a 10 dias úteis.";

    /** chave do setting => [título, view original da página] */
    public const PAGES = [
        'policy_cancellation_page' => ['Página "Políticas de Cancelamento"', 'cancellation-policy', '/politicas-de-cancelamento'],
        'policy_terms_page' => ['Página "Termos e Condições"', 'terms', '/termos-e-condicoes'],
        'policy_privacy_page' => ['Página "Política de Privacidade"', 'privacy-policy', '/politicas-de-privacidade'],
    ];

    private Setting $settingModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireManager();
        $this->settingModel = new Setting();
    }

    public function index(Request $request, Response $response): void
    {
        $pages = [];
        foreach (self::PAGES as $key => [$title, $view, $url]) {
            $custom = (string) $this->settingModel->get($key, '');
            $pages[$key] = [
                'title' => $title,
                'url' => $url,
                'value' => $custom !== '' ? $custom : self::extractOriginalText($view),
                'is_custom' => $custom !== '',
            ];
        }

        $payment = (string) $this->settingModel->get('policy_payment_text', '');
        $cancel = (string) $this->settingModel->get('policy_cancellation_summary', '');

        $this->view('admin/policies/index', [
            'exchangeRate' => (string) $this->settingModel->get('pagbank_usd_brl_rate', '5.50'),
            'leadMinutes' => (int) $this->settingModel->get('transfer_pickup_lead_minutes', '240'),
            'paymentText' => $payment !== '' ? $payment : self::DEFAULT_PAYMENT_TEXT,
            'cancellationSummary' => $cancel !== '' ? $cancel : self::DEFAULT_CANCELLATION_SUMMARY,
            'pages' => $pages,
            'pageTitle' => 'Políticas e Câmbio',
        ], 'admin');
    }

    public function save(Request $request, Response $response): void
    {
        // Câmbio: aceita "6,00" ou "6.00"
        $rateRaw = str_replace(',', '.', trim((string) $request->input('exchange_rate', '')));
        if ($rateRaw !== '') {
            if (!is_numeric($rateRaw) || (float) $rateRaw <= 0 || (float) $rateRaw > 100) {
                $this->flash('error', 'Taxa de câmbio inválida. Use um número como 6,00.');
                $this->redirect('/admin/politicas');
                return;
            }
            $this->settingModel->setWithGroup('pagbank_usd_brl_rate', number_format((float) $rateRaw, 2, '.', ''), 'payments');
        }

        // Antecedência padrão do transfer de saída (horas → minutos)
        $leadHours = str_replace(',', '.', trim((string) $request->input('lead_hours', '')));
        if ($leadHours !== '' && is_numeric($leadHours) && (float) $leadHours >= 0 && (float) $leadHours <= 12) {
            $this->settingModel->setWithGroup('transfer_pickup_lead_minutes', (string) (int) round((float) $leadHours * 60), 'transfers');
        }

        // Textos da página do passeio: se igual ao padrão, salva vazio (usa o padrão)
        $payment = $this->cleanText($request->input('payment_text', ''));
        $this->settingModel->setWithGroup('policy_payment_text', $payment === $this->cleanText(self::DEFAULT_PAYMENT_TEXT) ? '' : $payment, 'policies');

        $cancel = $this->cleanText($request->input('cancellation_summary', ''));
        $this->settingModel->setWithGroup('policy_cancellation_summary', $cancel === $this->cleanText(self::DEFAULT_CANCELLATION_SUMMARY) ? '' : $cancel, 'policies');

        // Páginas: se o texto não mudou em relação ao original, mantém a página original (com imagens/botões)
        foreach (self::PAGES as $key => [, $view]) {
            if ($request->input('restore_' . $key)) {
                $this->settingModel->setWithGroup($key, '', 'policies');
                continue;
            }
            $value = $this->cleanText($request->input($key, ''));
            $original = $this->cleanText(self::extractOriginalText($view));
            $this->settingModel->setWithGroup($key, $value === $original ? '' : $value, 'policies');
        }

        $this->app->reloadSettings();
        \App\Services\AuditService::getInstance()->logSettingsUpdate(
            ['pagbank_usd_brl_rate' => $rateRaw],
            'Atualizou políticas comerciais / câmbio'
        );

        $this->flash('success', 'Políticas e câmbio salvos! O site já mostra os novos valores.');
        $this->redirect('/admin/politicas');
    }

    private function cleanText(mixed $value): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", (string) $value);
        return trim(mb_substr($text, 0, 60000));
    }

    /** Extrai o texto atual da página original (view) para pré-preencher o editor. */
    public static function extractOriginalText(string $view): string
    {
        $file = BASE_PATH . '/resources/views/frontend/pages/' . $view . '.php';
        if (!is_file($file)) return '';
        $html = (string) file_get_contents($file);
        $start = strpos($html, '<div class="legal-content">');
        if ($start === false) return '';
        $html = substr($html, $start + strlen('<div class="legal-content">'));
        $end = strrpos($html, '</section>');
        if ($end !== false) $html = substr($html, 0, $end);
        return html_to_simple_text($html);
    }
}
