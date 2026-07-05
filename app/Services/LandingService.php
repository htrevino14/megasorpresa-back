<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AgeGroup;
use App\Models\AnnouncementBar;
use App\Models\CategoryCarouselItem;
use App\Models\FooterSection;
use App\Models\HeroSlide;
use App\Models\MegamenuCategory;
use App\Models\NewsletterCategory;
use App\Models\PaymentMethod;
use App\Models\SocialLink;
use Illuminate\Database\Eloquent\Collection;

class LandingService
{
    /**
     * Obtiene la barra de anuncio activa más reciente, o null si no hay ninguna.
     */
    public function getActiveAnnouncementBar(): ?AnnouncementBar
    {
        return AnnouncementBar::active()->latest()->first();
    }

    /**
     * Obtiene los slides activos del hero banner ordenados por sort_order.
     */
    public function getHeroSlides(): Collection
    {
        return HeroSlide::active()->orderBy('sort_order')->get();
    }

    /**
     * Obtiene las categorías activas del megaménú con sus grupos, ítems y panel promocional.
     */
    public function getMegamenu(): Collection
    {
        return MegamenuCategory::active()
            ->with(['subcategoryGroups.items', 'promoPanel'])
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Obtiene los ítems activos del carrusel de categorías ordenados por sort_order.
     */
    public function getCategoryCarousel(): Collection
    {
        return CategoryCarouselItem::active()->orderBy('sort_order')->get();
    }

    /**
     * Obtiene los grupos de edad activos ordenados por sort_order.
     */
    public function getAgeGroups(): Collection
    {
        return AgeGroup::active()->orderBy('sort_order')->get();
    }

    /**
     * Obtiene los datos completos del footer: secciones con sus enlaces activos,
     * redes sociales y métodos de pago.
     *
     * @return array{sections: Collection, social_links: Collection, payment_methods: Collection}
     */
    public function getFooter(): array
    {
        $sections = FooterSection::active()
            ->with(['links' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return [
            'sections' => $sections,
            'social_links' => SocialLink::active()->orderBy('sort_order')->get(),
            'payment_methods' => PaymentMethod::active()->orderBy('sort_order')->get(),
        ];
    }

    /**
     * Obtiene las categorías activas del boletín ordenadas por sort_order.
     */
    public function getNewsletterCategories(): Collection
    {
        return NewsletterCategory::active()->orderBy('sort_order')->get();
    }
}
