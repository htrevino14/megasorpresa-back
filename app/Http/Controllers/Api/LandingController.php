<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscribeNewsletterRequest;
use App\Http\Resources\AgeGroupResource;
use App\Http\Resources\AnnouncementBarResource;
use App\Http\Resources\CategoryCarouselItemResource;
use App\Http\Resources\FooterSectionResource;
use App\Http\Resources\HeroSlideResource;
use App\Http\Resources\MegamenuCategoryResource;
use App\Http\Resources\NewsletterCategoryResource;
use App\Http\Resources\PaymentMethodResource;
use App\Http\Resources\SocialLinkResource;
use App\Models\AgeGroup;
use App\Models\AnnouncementBar;
use App\Models\CategoryCarouselItem;
use App\Models\FooterSection;
use App\Models\HeroSlide;
use App\Models\MegamenuCategory;
use App\Models\NewsletterCategory;
use App\Models\PaymentMethod;
use App\Models\SocialLink;
use App\Services\NewsletterService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class LandingController extends Controller
{
    public function __construct(
        private NewsletterService $newsletterService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/landing/announcement-bar",
     *     summary="Obtener barra de anuncio activa",
     *     description="Retorna la barra de anuncio activa más reciente para mostrar en el header de la landing page. Sólo se devuelve un registro: el activo con programación vigente.",
     *     tags={"Landing Page"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Barra de anuncio activa",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 nullable=true,
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="message", type="string", example="Entrega en locker 24/7 ahora disponible."),
     *                 @OA\Property(property="link_url", type="string", nullable=true, example="/lockers"),
     *                 @OA\Property(property="link_label", type="string", nullable=true, example="Saber más"),
     *                 @OA\Property(property="bg_color", type="string", nullable=true, example="#111827"),
     *                 @OA\Property(property="text_color", type="string", nullable=true, example="#ffffff")
     *             )
     *         )
     *     )
     * )
     */
    public function announcementBar(): JsonResponse
    {
        $bar = AnnouncementBar::active()->latest()->first();

        return response()->json([
            'data' => $bar ? new AnnouncementBarResource($bar) : null,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/landing/hero-slides",
     *     summary="Listar slides del hero banner",
     *     description="Retorna los slides activos del hero banner, ordenados por sort_order ascendente. Incluye URLs de imagen para desktop y mobile.",
     *     tags={"Landing Page"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Listado de hero slides activos",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Gran valor – 2 x $299"),
     *                     @OA\Property(property="subtitle", type="string", example="Autos de control remoto escala 1:24"),
     *                     @OA\Property(property="cta_text", type="string", example="Comprar ahora"),
     *                     @OA\Property(property="cta_link", type="string", example="/category/juguetes"),
     *                     @OA\Property(property="image_url_desktop", type="string", example="https://cdn.megasorpresa.com/hero/desktop-1.jpg"),
     *                     @OA\Property(property="image_url_mobile", type="string", nullable=true, example="https://cdn.megasorpresa.com/hero/mobile-1.jpg"),
     *                     @OA\Property(property="alt_text", type="string", nullable=true, example="Autos de juguete RC"),
     *                     @OA\Property(property="bg_color", type="string", nullable=true, example="#0072E3"),
     *                     @OA\Property(property="sort_order", type="integer", example=1)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function heroSlides(): JsonResponse
    {
        $slides = HeroSlide::active()->orderBy('sort_order')->get();

        return response()->json([
            'data' => HeroSlideResource::collection($slides),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/landing/megamenu",
     *     summary="Obtener estructura completa del megaménú",
     *     description="Retorna las categorías activas del megaménú con sus grupos de subcategorías, ítems y panel promocional. Útil para renderizar la navegación principal del header.",
     *     tags={"Landing Page"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Estructura del megaménú",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Juguetes"),
     *                     @OA\Property(property="slug", type="string", example="juguetes"),
     *                     @OA\Property(property="icon", type="string", nullable=true, example="🎠"),
     *                     @OA\Property(property="sort_order", type="integer", example=1),
     *                     @OA\Property(
     *                         property="subcategory_groups",
     *                         type="array",
     *
     *                         @OA\Items(
     *                             type="object",
     *
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="title", type="string", example="Acción y aventura"),
     *                             @OA\Property(property="sort_order", type="integer", example=1),
     *                             @OA\Property(
     *                                 property="items",
     *                                 type="array",
     *
     *                                 @OA\Items(
     *                                     type="object",
     *
     *                                     @OA\Property(property="id", type="integer", example=1),
     *                                     @OA\Property(property="label", type="string", example="Superhéroes"),
     *                                     @OA\Property(property="sort_order", type="integer", example=1)
     *                                 )
     *                             )
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="promo_panel",
     *                         type="object",
     *                         nullable=true,
     *                         @OA\Property(property="badge", type="string", example="Nuevo"),
     *                         @OA\Property(property="title", type="string", example="Colección Primavera 2025"),
     *                         @OA\Property(property="description", type="string", example="Descubre los juguetes más populares de la temporada."),
     *                         @OA\Property(property="emoji", type="string", example="🎠"),
     *                         @OA\Property(property="bg_color", type="string", example="from-yellow-400 to-orange-400"),
     *                         @OA\Property(property="link_text", type="string", example="Ver colección"),
     *                         @OA\Property(property="link_url", type="string", example="/category/juguetes"),
     *                         @OA\Property(property="image_url", type="string", nullable=true, example="https://cdn.megasorpresa.com/promo/juguetes.jpg")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function megamenu(): JsonResponse
    {
        $categories = MegamenuCategory::active()
            ->with(['subcategoryGroups.items', 'promoPanel'])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => MegamenuCategoryResource::collection($categories),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/landing/category-carousel",
     *     summary="Listar ítems del carrusel de categorías",
     *     description="Retorna los ítems activos del carrusel de categorías destacadas (sección 'Top categorías'), ordenados por sort_order ascendente.",
     *     tags={"Landing Page"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Listado de ítems del carrusel",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Pokémon"),
     *                     @OA\Property(property="slug", type="string", example="pokemon"),
     *                     @OA\Property(property="image_url", type="string", example="https://cdn.megasorpresa.com/carousel/pokemon.jpg"),
     *                     @OA\Property(property="bg_color", type="string", example="bg-green-50"),
     *                     @OA\Property(property="sort_order", type="integer", example=1),
     *                     @OA\Property(property="category_id", type="integer", nullable=true, example=5)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function categoryCarousel(): JsonResponse
    {
        $items = CategoryCarouselItem::active()->orderBy('sort_order')->get();

        return response()->json([
            'data' => CategoryCarouselItemResource::collection($items),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/landing/age-groups",
     *     summary="Listar grupos de edad",
     *     description="Retorna los grupos de edad activos para la sección 'Comprar por edad', ordenados por sort_order ascendente.",
     *     tags={"Landing Page"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Listado de grupos de edad",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="label", type="string", example="0-18"),
     *                     @OA\Property(property="sublabel", type="string", example="MESES"),
     *                     @OA\Property(property="slug", type="string", example="0-18-meses"),
     *                     @OA\Property(property="bg_color", type="string", example="#b2f0e8"),
     *                     @OA\Property(property="text_color", type="string", example="#065f46"),
     *                     @OA\Property(property="sort_order", type="integer", example=1),
     *                     @OA\Property(property="category_id_destination", type="integer", nullable=true, example=3)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function ageGroups(): JsonResponse
    {
        $groups = AgeGroup::active()->orderBy('sort_order')->get();

        return response()->json([
            'data' => AgeGroupResource::collection($groups),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/landing/footer",
     *     summary="Obtener datos completos del footer",
     *     description="Retorna toda la información necesaria para renderizar el footer: secciones con sus enlaces, redes sociales y métodos de pago activos.",
     *     tags={"Landing Page"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Datos del footer",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="sections",
     *                     type="array",
     *                     description="Columnas de información del footer con sus enlaces",
     *
     *                     @OA\Items(
     *                         type="object",
     *
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Información al cliente"),
     *                         @OA\Property(property="sort_order", type="integer", example=1),
     *                         @OA\Property(
     *                             property="links",
     *                             type="array",
     *
     *                             @OA\Items(
     *                                 type="object",
     *
     *                                 @OA\Property(property="id", type="integer", example=1),
     *                                 @OA\Property(property="label", type="string", example="Solicitar catálogo"),
     *                                 @OA\Property(property="url", type="string", example="/catalogue"),
     *                                 @OA\Property(property="icon", type="string", nullable=true),
     *                                 @OA\Property(property="open_in_new_tab", type="boolean", example=false),
     *                                 @OA\Property(property="sort_order", type="integer", example=1)
     *                             )
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="social_links",
     *                     type="array",
     *                     description="Redes sociales activas",
     *
     *                     @OA\Items(
     *                         type="object",
     *
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="platform", type="string", example="Facebook"),
     *                         @OA\Property(property="url", type="string", example="https://facebook.com/megasorpresa"),
     *                         @OA\Property(property="icon_class", type="string", nullable=true),
     *                         @OA\Property(property="icon_svg", type="string", nullable=true),
     *                         @OA\Property(property="initial", type="string", example="f"),
     *                         @OA\Property(property="sort_order", type="integer", example=1)
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="payment_methods",
     *                     type="array",
     *                     description="Métodos de pago aceptados",
     *
     *                     @OA\Items(
     *                         type="object",
     *
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Visa"),
     *                         @OA\Property(property="logo_url", type="string", nullable=true),
     *                         @OA\Property(property="icon_class", type="string", nullable=true),
     *                         @OA\Property(property="sort_order", type="integer", example=1)
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function footer(): JsonResponse
    {
        $sections = FooterSection::active()
            ->with(['links' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        $socialLinks = SocialLink::active()->orderBy('sort_order')->get();
        $paymentMethods = PaymentMethod::active()->orderBy('sort_order')->get();

        return response()->json([
            'data' => [
                'sections' => FooterSectionResource::collection($sections),
                'social_links' => SocialLinkResource::collection($socialLinks),
                'payment_methods' => PaymentMethodResource::collection($paymentMethods),
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/landing/newsletter-categories",
     *     summary="Listar categorías del boletín",
     *     description="Retorna las categorías activas disponibles para la suscripción al boletín de correo.",
     *     tags={"Landing Page"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Listado de categorías del boletín",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="label", type="string", example="Juguetes"),
     *                     @OA\Property(property="slug", type="string", example="juguetes"),
     *                     @OA\Property(property="sort_order", type="integer", example=1)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function newsletterCategories(): JsonResponse
    {
        $categories = NewsletterCategory::active()->orderBy('sort_order')->get();

        return response()->json([
            'data' => NewsletterCategoryResource::collection($categories),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/landing/newsletter/subscribe",
     *     summary="Suscribirse al boletín de correo",
     *     description="Suscribe al usuario autenticado a una o más categorías del boletín. Si ya está suscrito a alguna categoría, se omite sin error (idempotente).",
     *     tags={"Landing Page"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"category_slugs"},
     *
     *             @OA\Property(
     *                 property="category_slugs",
     *                 type="array",
     *                 minItems=1,
     *                 description="Slugs de las categorías a las que se desea suscribir",
     *
     *                 @OA\Items(type="string", example="juguetes"),
     *                 example={"juguetes", "gaming"}
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Suscripción realizada exitosamente",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Suscripción realizada exitosamente."),
     *             @OA\Property(
     *                 property="subscribed_categories",
     *                 type="array",
     *
     *                 @OA\Items(type="string", example="juguetes"),
     *                 example={"juguetes", "gaming"}
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Datos de validación inválidos",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     */
    public function subscribeNewsletter(SubscribeNewsletterRequest $request): JsonResponse
    {
        $slugs = $this->newsletterService->subscribeToCategories(
            $request->user(),
            $request->input('category_slugs')
        );

        return response()->json([
            'message' => 'Suscripción realizada exitosamente.',
            'subscribed_categories' => $slugs,
        ]);
    }
}
