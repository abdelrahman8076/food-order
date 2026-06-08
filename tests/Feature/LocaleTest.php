<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_locale_switch_sets_arabic_session_and_rtl_layout(): void
    {
        $response = $this->post(route('locale.switch', 'ar'));

        $response->assertRedirect();
        $this->assertEquals('ar', session('locale'));

        $home = $this->get(route('home'));

        $home->assertOk();
        $home->assertSee('dir="rtl"', false);
        $home->assertSee('lang="ar"', false);
    }

    public function test_item_returns_arabic_name_when_locale_is_ar(): void
    {
        $category = Category::factory()->create([
            'name' => 'Burgers',
            'name_ar' => 'برجر',
        ]);

        $item = Item::factory()->create([
            'category_id' => $category->id,
            'name' => 'Classic Burger',
            'name_ar' => 'برجر كلاسيك',
        ]);

        app()->setLocale('ar');

        $this->assertSame('برجر كلاسيك', $item->localizedName());
        $this->assertSame('برجر', $category->localizedName());
    }

    public function test_order_stores_localized_item_name_at_checkout(): void
    {
        $item = Item::factory()->create([
            'name' => 'Classic Burger',
            'name_ar' => 'برجر كلاسيك',
            'is_available' => true,
        ]);

        app()->setLocale('ar');

        $order = app(OrderService::class)->createOrder(
            [$item->id => ['quantity' => 1, 'notes' => null]],
            [
                'customer_name' => 'Test User',
                'customer_phone' => '01000000000',
                'delivery_city' => 'Cairo',
                'delivery_area' => 'Nasr City',
                'delivery_street' => 'Main St',
                'delivery_building' => '1',
            ],
        );

        $this->assertSame('برجر كلاسيك', $order->orderItems->first()->item_name);
    }

    public function test_invalid_locale_returns_not_found(): void
    {
        $this->post(route('locale.switch', 'fr'))->assertNotFound();
    }
}
