<?php
declare(strict_types=1);

use App\Controllers\Frontend\HomeController;
use App\Controllers\Frontend\TripsController;
use App\Controllers\Frontend\TransferController;
use App\Controllers\Frontend\CartController;
use App\Controllers\Frontend\CheckoutController;
use App\Controllers\Frontend\AccountController;
use App\Controllers\Frontend\PageController;
use App\Controllers\Frontend\BlogController;
use App\Controllers\Auth\LoginController;
use App\Controllers\Auth\RegisterController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\TripsController as AdminTripsController;
use App\Controllers\Admin\CategoriesController as AdminCategoriesController;
use App\Controllers\Admin\TransfersController as AdminTransfersController;
use App\Controllers\Admin\BookingsController as AdminBookingsController;
use App\Controllers\Admin\VouchersController as AdminVouchersController;
use App\Controllers\Admin\AffiliatesController as AdminAffiliatesController;
use App\Controllers\Admin\UsersController as AdminUsersController;
use App\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Controllers\Admin\NewsletterController as AdminNewsletterController;
use App\Controllers\Api\TransferSearchController;
use App\Controllers\Api\PricingController;
use App\Controllers\Api\CartController as ApiCartController;
use App\Controllers\Api\WebhookController;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;
use App\Middleware\CsrfMiddleware;

/**
 * Definição de todas as rotas do sistema.
 * @var \Core\Router $router
 */

// ============================================================
// FRONTEND - Páginas Públicas
// ============================================================
$router->get('/', [HomeController::class, 'index'], [], 'home');
$router->get('/passeios', [TripsController::class, 'index'], [], 'trips.index');
$router->get('/passeios/{slug}', [TripsController::class, 'show'], [], 'trips.show');
$router->get('/transfers', [TransferController::class, 'index'], [], 'transfers.index');
$router->get('/sobre-nos', [PageController::class, 'about'], [], 'pages.about');
$router->get('/contato', [PageController::class, 'contact'], [], 'pages.contact');
$router->post('/contato', [PageController::class, 'sendContact'], [CsrfMiddleware::class], 'pages.contact.send');
$router->get('/pesquisa', [PageController::class, 'search'], [], 'pages.search');
$router->get('/termos-e-condicoes', [PageController::class, 'terms'], [], 'pages.terms');
$router->get('/termos-afiliados', [PageController::class, 'affiliateTerms'], [], 'pages.affiliate_terms');
$router->get('/politicas-de-cancelamento', [PageController::class, 'cancellationPolicy'], [], 'pages.cancellation_policy');
$router->get('/politicas-de-privacidade', [PageController::class, 'privacyPolicy'], [], 'pages.privacy_policy');
$router->get('/programa-de-afiliados', [PageController::class, 'affiliateProgram'], [], 'pages.affiliate_program');
$router->get('/cadastro-afiliado', [PageController::class, 'affiliateRegister'], [], 'pages.affiliate_register');
$router->post('/cadastro-afiliado', [PageController::class, 'affiliateRegisterStore'], [CsrfMiddleware::class], 'pages.affiliate_register.store');
$router->get('/login-afiliado', [PageController::class, 'affiliateLogin'], [], 'pages.affiliate_login');

// Painel do Afiliado (autenticado)
$router->group(['prefix' => '/painel-afiliado', 'middleware' => [AuthMiddleware::class]], function ($router) {
    $router->get('', [AccountController::class, 'affiliateDashboard'], [], 'affiliate.dashboard');
    $router->get('/links', [AccountController::class, 'affiliateLinks'], [], 'affiliate.links');
    $router->get('/comissoes', [AccountController::class, 'affiliateCommissions'], [], 'affiliate.commissions');
    $router->get('/visitas', [AccountController::class, 'affiliateVisits'], [], 'affiliate.visits');
    $router->get('/criativos', [AccountController::class, 'affiliateCreatives'], [], 'affiliate.creatives');
    $router->get('/pagamentos', [AccountController::class, 'affiliatePayments'], [], 'affiliate.payments');
    $router->get('/configuracoes', [AccountController::class, 'affiliateSettings'], [], 'affiliate.settings');
    $router->post('/configuracoes', [AccountController::class, 'affiliateSettingsUpdate'], [CsrfMiddleware::class], 'affiliate.settings.update');
    $router->get('/landing-page', [AccountController::class, 'affiliateLanding'], [], 'affiliate.landing');
});
$router->get('/blog', [BlogController::class, 'index'], [], 'blog.index');
$router->get('/blog/categoria/{slug}', [BlogController::class, 'category'], [], 'blog.category');
$router->get('/blog/{slug}', [BlogController::class, 'show'], [], 'blog.show');
$router->get('/pagina/{slug}', [PageController::class, 'show'], [], 'pages.show');

// Carrinho
$router->get('/carrinho', [CartController::class, 'index'], [], 'cart.index');
$router->post('/carrinho/adicionar', [CartController::class, 'add'], [CsrfMiddleware::class], 'cart.add');
$router->post('/carrinho/remover', [CartController::class, 'remove'], [CsrfMiddleware::class], 'cart.remove');
$router->post('/carrinho/limpar', [CartController::class, 'clear'], [CsrfMiddleware::class], 'cart.clear');

// Checkout
$router->get('/checkout', [CheckoutController::class, 'index'], [], 'checkout.index');
$router->post('/checkout/processar', [CheckoutController::class, 'process'], [CsrfMiddleware::class], 'checkout.process');
$router->get('/checkout/sucesso/{booking_number}', [CheckoutController::class, 'success'], [], 'checkout.success');
$router->get('/transfer-obrigado', [CheckoutController::class, 'transferSuccess'], [], 'checkout.transfer_success');

// ============================================================
// AUTENTICAÇÃO
// ============================================================
$router->get('/login', [LoginController::class, 'showLogin'], [], 'login');
$router->post('/login', [LoginController::class, 'login'], [CsrfMiddleware::class], 'login.post');
$router->get('/registrar', [RegisterController::class, 'showRegister'], [], 'register');
$router->post('/registrar', [RegisterController::class, 'register'], [CsrfMiddleware::class], 'register.post');
$router->get('/esqueci-senha', [LoginController::class, 'showForgotPassword'], [], 'password.forgot');
$router->post('/esqueci-senha', [LoginController::class, 'forgotPassword'], [CsrfMiddleware::class], 'password.forgot.post');
$router->get('/resetar-senha/{token}', [LoginController::class, 'showResetPassword'], [], 'password.reset');
$router->post('/resetar-senha', [LoginController::class, 'resetPassword'], [CsrfMiddleware::class], 'password.reset.post');
$router->get('/logout', [LoginController::class, 'logout'], [], 'logout');

// ============================================================
// ÁREA DO CLIENTE (autenticado)
// ============================================================
$router->group(['prefix' => '/minha-conta', 'middleware' => [AuthMiddleware::class]], function ($router) {
    $router->get('', [AccountController::class, 'dashboard'], [], 'account.dashboard');
    $router->get('/reservas', [AccountController::class, 'bookings'], [], 'account.bookings');
    $router->get('/reservas/{id}', [AccountController::class, 'bookingDetail'], [], 'account.booking.detail');
    $router->get('/transfers', [AccountController::class, 'transfers'], [], 'account.transfers');
    $router->get('/wishlist', [AccountController::class, 'wishlist'], [], 'account.wishlist');
    $router->post('/wishlist/toggle', [AccountController::class, 'toggleWishlist'], [CsrfMiddleware::class], 'account.wishlist.toggle');
    $router->get('/cancelamentos', [AccountController::class, 'cancellations'], [], 'account.cancellations');
    $router->post('/cancelamentos/solicitar', [AccountController::class, 'requestCancellation'], [CsrfMiddleware::class], 'account.cancellations.request');
    $router->get('/cobranca', [AccountController::class, 'billing'], [], 'account.billing');
    $router->post('/cobranca', [AccountController::class, 'updateBilling'], [CsrfMiddleware::class], 'account.billing.update');
    $router->get('/perfil', [AccountController::class, 'profile'], [], 'account.profile');
    $router->post('/perfil', [AccountController::class, 'updateProfile'], [CsrfMiddleware::class], 'account.profile.update');
    $router->post('/perfil/senha', [AccountController::class, 'updatePassword'], [CsrfMiddleware::class], 'account.password.update');
    $router->get('/voucher/{reference}', [AccountController::class, 'downloadVoucher'], [], 'account.voucher.download');
});

// ============================================================
// API (AJAX endpoints)
// ============================================================
$router->group(['prefix' => '/api'], function ($router) {
    $router->post('/transfers/buscar', [TransferSearchController::class, 'search'], [], 'api.transfers.search');
    $router->post('/newsletter/subscribe', [ApiCartController::class, 'newsletterSubscribe'], [], 'api.newsletter.subscribe');
    $router->post('/pricing/day-prices', [PricingController::class, 'getDayPrices'], [], 'api.pricing.day');
    $router->post('/cart/add-transfer', [ApiCartController::class, 'addTransfer'], [], 'api.cart.add_transfer');
    $router->post('/cart/remove-transfer', [ApiCartController::class, 'removeTransfer'], [], 'api.cart.remove_transfer');
    $router->get('/cart/count', [ApiCartController::class, 'count'], [], 'api.cart.count');
    $router->post('/webhook/payment', [WebhookController::class, 'handlePayment'], [], 'api.webhook.payment');
    $router->post('/webhook/stripe', [WebhookController::class, 'handleStripe'], [], 'api.webhook.stripe');
});

// ============================================================
// ADMIN (painel administrativo)
// ============================================================
$router->group(['prefix' => '/admin', 'middleware' => [AuthMiddleware::class, AdminMiddleware::class]], function ($router) {
    // Dashboard
    $router->get('', [DashboardController::class, 'index'], [], 'admin.dashboard');

    // Passeios
    $router->get('/passeios', [AdminTripsController::class, 'index'], [], 'admin.trips.index');
    $router->get('/passeios/criar', [AdminTripsController::class, 'create'], [], 'admin.trips.create');
    $router->post('/passeios/criar', [AdminTripsController::class, 'store'], [CsrfMiddleware::class], 'admin.trips.store');
    $router->get('/passeios/{id}/editar', [AdminTripsController::class, 'edit'], [], 'admin.trips.edit');
    $router->post('/passeios/{id}/editar', [AdminTripsController::class, 'update'], [CsrfMiddleware::class], 'admin.trips.update');
    $router->post('/passeios/{id}/excluir', [AdminTripsController::class, 'destroy'], [CsrfMiddleware::class], 'admin.trips.destroy');
    $router->get('/passeios/{id}/precos', [AdminTripsController::class, 'pricing'], [], 'admin.trips.pricing');
    $router->post('/passeios/{id}/precos', [AdminTripsController::class, 'savePricing'], [CsrfMiddleware::class], 'admin.trips.pricing.save');

    // Categorias de Passeios
    $router->get('/categorias', [AdminCategoriesController::class, 'index'], [], 'admin.categories.index');
    $router->get('/categorias/criar', [AdminCategoriesController::class, 'create'], [], 'admin.categories.create');
    $router->post('/categorias/criar', [AdminCategoriesController::class, 'store'], [CsrfMiddleware::class], 'admin.categories.store');
    $router->get('/categorias/{id}/editar', [AdminCategoriesController::class, 'edit'], [], 'admin.categories.edit');
    $router->post('/categorias/{id}/editar', [AdminCategoriesController::class, 'update'], [CsrfMiddleware::class], 'admin.categories.update');
    $router->post('/categorias/{id}/excluir', [AdminCategoriesController::class, 'destroy'], [CsrfMiddleware::class], 'admin.categories.destroy');

    // Transfers
    $router->get('/transfers', [AdminTransfersController::class, 'index'], [], 'admin.transfers.index');
    $router->get('/transfers/veiculos', [AdminTransfersController::class, 'vehicles'], [], 'admin.transfers.vehicles');
    $router->get('/transfers/veiculos/criar', [AdminTransfersController::class, 'createVehicle'], [], 'admin.transfers.vehicles.create');
    $router->post('/transfers/veiculos/criar', [AdminTransfersController::class, 'storeVehicle'], [CsrfMiddleware::class], 'admin.transfers.vehicles.store');
    $router->get('/transfers/veiculos/{id}/editar', [AdminTransfersController::class, 'editVehicle'], [], 'admin.transfers.vehicles.edit');
    $router->post('/transfers/veiculos/{id}/editar', [AdminTransfersController::class, 'updateVehicle'], [CsrfMiddleware::class], 'admin.transfers.vehicles.update');
    $router->get('/transfers/locais', [AdminTransfersController::class, 'locations'], [], 'admin.transfers.locations');
    $router->post('/transfers/locais/criar', [AdminTransfersController::class, 'storeLocation'], [CsrfMiddleware::class], 'admin.transfers.locations.store');
    $router->post('/transfers/locais/{id}/editar', [AdminTransfersController::class, 'updateLocation'], [CsrfMiddleware::class], 'admin.transfers.locations.update');
    $router->get('/transfers/reservas', [AdminTransfersController::class, 'bookings'], [], 'admin.transfers.bookings');

    // Bookings
    $router->get('/reservas', [AdminBookingsController::class, 'index'], [], 'admin.bookings.index');
    $router->get('/reservas/criar', [AdminBookingsController::class, 'create'], [], 'admin.bookings.create');
    $router->post('/reservas/criar', [AdminBookingsController::class, 'store'], [CsrfMiddleware::class], 'admin.bookings.store');
    $router->get('/reservas/{id}', [AdminBookingsController::class, 'show'], [], 'admin.bookings.show');
    $router->post('/reservas/{id}/status', [AdminBookingsController::class, 'updateStatus'], [CsrfMiddleware::class], 'admin.bookings.status');

    // Vouchers
    $router->get('/vouchers', [AdminVouchersController::class, 'index'], [], 'admin.vouchers.index');
    $router->get('/vouchers/{id}/visualizar', [AdminVouchersController::class, 'view'], [], 'admin.vouchers.view');
    $router->get('/vouchers/{id}/download', [AdminVouchersController::class, 'download'], [], 'admin.vouchers.download');
    $router->post('/vouchers/{id}/enviar', [AdminVouchersController::class, 'send'], [CsrfMiddleware::class], 'admin.vouchers.send');

    // Afiliados
    $router->get('/afiliados', [AdminAffiliatesController::class, 'index'], [], 'admin.affiliates.index');
    $router->post('/afiliados/{id}/aprovar', [AdminAffiliatesController::class, 'approve'], [CsrfMiddleware::class], 'admin.affiliates.approve');
    $router->post('/afiliados/{id}/rejeitar', [AdminAffiliatesController::class, 'reject'], [CsrfMiddleware::class], 'admin.affiliates.reject');
    $router->get('/afiliados/comissoes', [AdminAffiliatesController::class, 'commissions'], [], 'admin.affiliates.commissions');
    $router->post('/afiliados/comissoes/{id}/pagar', [AdminAffiliatesController::class, 'payCommission'], [CsrfMiddleware::class], 'admin.affiliates.pay');

    // Usuários
    $router->get('/usuarios', [AdminUsersController::class, 'index'], [], 'admin.users.index');
    $router->get('/usuarios/criar', [AdminUsersController::class, 'create'], [], 'admin.users.create');
    $router->post('/usuarios/criar', [AdminUsersController::class, 'store'], [CsrfMiddleware::class], 'admin.users.store');
    $router->get('/usuarios/{id}/editar', [AdminUsersController::class, 'edit'], [], 'admin.users.edit');
    $router->post('/usuarios/{id}/editar', [AdminUsersController::class, 'update'], [CsrfMiddleware::class], 'admin.users.update');
    $router->post('/usuarios/{id}/excluir', [AdminUsersController::class, 'destroy'], [CsrfMiddleware::class], 'admin.users.destroy');
    $router->get('/usuarios/{id}/impersonate', [AdminUsersController::class, 'impersonate'], [], 'admin.users.impersonate');

    // Configurações
    $router->get('/configuracoes', [AdminSettingsController::class, 'index'], [], 'admin.settings.index');
    $router->post('/configuracoes', [AdminSettingsController::class, 'update'], [CsrfMiddleware::class], 'admin.settings.update');
    $router->post('/configuracoes/email-teste', [AdminSettingsController::class, 'testEmail'], [CsrfMiddleware::class], 'admin.settings.test_email');

    // Newsletter
    $router->get('/newsletter', [AdminNewsletterController::class, 'index'], [], 'admin.newsletter.index');
    $router->post('/newsletter/{id}/excluir', [AdminNewsletterController::class, 'destroy'], [CsrfMiddleware::class], 'admin.newsletter.destroy');
    $router->get('/newsletter/exportar', [AdminNewsletterController::class, 'export'], [], 'admin.newsletter.export');
    $router->get('/newsletter/campanhas', [AdminNewsletterController::class, 'createCampaign'], [], 'admin.newsletter.campaigns');
    $router->post('/newsletter/campanhas/enviar', [AdminNewsletterController::class, 'sendCampaign'], [CsrfMiddleware::class], 'admin.newsletter.send');
});
