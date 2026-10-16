<?php

namespace Tests\Feature;

use App\Models\Bus;
use App\Models\Driver;
use App\Models\ShiftLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ShiftDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_shift_detail_page_renders_with_selected_shift(): void
    {
        $admin = User::create([
            'name' => 'Admin Dispatcher',
            'email' => 'admin-' . uniqid() . '@lancotri.local',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $driverUser = User::create([
            'name' => 'Carlos Ray',
            'email' => 'carlos-' . uniqid() . '@lancotri.local',
            'password' => Hash::make('secret123'),
            'role' => 'driver',
            'status' => 'active',
        ]);

        $driver = Driver::create([
            'user_id' => $driverUser->id,
            'badge_id' => 'DRV-1001',
            'duty_status' => 'available',
        ]);

        $bus = Bus::create([
            'bus_number' => '42',
            'plate' => 'TX-9021',
            'status' => 'active',
            'driver_id' => $driver->id,
            'odometer' => 125000,
        ]);

        $shift = ShiftLog::create([
            'shift_number' => 'SHF-0006',
            'bus_id' => $bus->id,
            'driver_id' => $driver->id,
            'start_km' => 125000,
            'end_km' => 125140,
            'distance_km' => 140.0,
            'status' => ShiftLog::STATUS_COMPLETED,
            'started_at' => now()->subHours(4),
            'ended_at' => now(),
        ]);

        // Direct browser visit to /shifts/{shift}
        $response = $this->actingAs($admin)->get(route('shifts.show', ['shift' => $shift->id]));

        $response->assertOk();
        $response->assertSee('Shift Management &amp; Meter Audits', false);
        $response->assertSee('Inspection Desk');
        $response->assertSee($shift->display_id);
        $response->assertSee('Carlos Ray');
        $response->assertSee('125,000 km');
        $response->assertSee('125,140 km');
    }

    public function test_shift_detail_json_response(): void
    {
        $admin = User::create([
            'name' => 'Admin Dispatcher',
            'email' => 'admin-' . uniqid() . '@lancotri.local',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $driverUser = User::create([
            'name' => 'Carlos Ray',
            'email' => 'carlos-' . uniqid() . '@lancotri.local',
            'password' => Hash::make('secret123'),
            'role' => 'driver',
            'status' => 'active',
        ]);

        $driver = Driver::create([
            'user_id' => $driverUser->id,
            'badge_id' => 'DRV-1001',
            'duty_status' => 'available',
        ]);

        $bus = Bus::create([
            'bus_number' => '42',
            'plate' => 'TX-9021',
            'status' => 'active',
            'driver_id' => $driver->id,
            'odometer' => 125000,
        ]);

        $shift = ShiftLog::create([
            'shift_number' => 'SHF-0006',
            'bus_id' => $bus->id,
            'driver_id' => $driver->id,
            'start_km' => 125000,
            'end_km' => 125140,
            'distance_km' => 140.0,
            'status' => ShiftLog::STATUS_COMPLETED,
            'started_at' => now()->subHours(4),
            'ended_at' => now(),
        ]);

        $response = $this->actingAs($admin)
            ->getJson(route('shifts.show', ['shift' => $shift->id]));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'shift' => [
                'id' => $shift->id,
                'shift_number' => 'SHF-0006',
                'start_km' => 125000,
                'end_km' => 125140,
            ],
        ]);
    }

    public function test_admin_can_verify_completed_shift(): void
    {
        $admin = User::create([
            'name' => 'Admin Dispatcher',
            'email' => 'admin-' . uniqid() . '@lancotri.local',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $bus = Bus::create([
            'bus_number' => '43',
            'plate' => 'TX-9022',
            'status' => 'active',
            'odometer' => 125000,
        ]);

        $shift = ShiftLog::create([
            'shift_number' => 'SHF-0007',
            'bus_id' => $bus->id,
            'start_km' => 125000,
            'end_km' => 125100,
            'distance_km' => 100.0,
            'status' => ShiftLog::STATUS_COMPLETED,
            'started_at' => now()->subHours(2),
            'ended_at' => now(),
        ]);

        $response = $this->actingAs($admin)
            ->postJson(route('shifts.verify', ['shift' => $shift->id]));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'status' => ShiftLog::STATUS_VERIFIED,
        ]);

        $this->assertEquals(ShiftLog::STATUS_VERIFIED, $shift->fresh()->status);
    }

    public function test_admin_cannot_verify_in_progress_shift(): void
    {
        $admin = User::create([
            'name' => 'Admin Dispatcher',
            'email' => 'admin-' . uniqid() . '@lancotri.local',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $bus = Bus::create([
            'bus_number' => '44',
            'plate' => 'TX-9023',
            'status' => 'active',
            'odometer' => 125000,
        ]);

        $shift = ShiftLog::create([
            'shift_number' => 'SHF-0008',
            'bus_id' => $bus->id,
            'start_km' => 125000,
            'end_km' => null,
            'status' => ShiftLog::STATUS_IN_PROGRESS,
            'started_at' => now()->subHour(),
        ]);

        $response = $this->actingAs($admin)
            ->postJson(route('shifts.verify', ['shift' => $shift->id]));

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);

        $this->assertEquals(ShiftLog::STATUS_IN_PROGRESS, $shift->fresh()->status);
    }

    public function test_admin_can_flag_and_revert_shift(): void
    {
        $admin = User::create([
            'name' => 'Admin Dispatcher',
            'email' => 'admin-' . uniqid() . '@lancotri.local',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $bus = Bus::create([
            'bus_number' => '45',
            'plate' => 'TX-9024',
            'status' => 'active',
            'odometer' => 125000,
        ]);

        $shift = ShiftLog::create([
            'shift_number' => 'SHF-0009',
            'bus_id' => $bus->id,
            'start_km' => 125000,
            'end_km' => 125050,
            'distance_km' => 50.0,
            'status' => ShiftLog::STATUS_COMPLETED,
            'started_at' => now()->subHours(3),
            'ended_at' => now(),
        ]);

        // Flag shift
        $flagResponse = $this->actingAs($admin)
            ->postJson(route('shifts.flag', ['shift' => $shift->id]), [
                'reason' => 'Odometer glare check',
            ]);

        $flagResponse->assertOk();
        $this->assertEquals(ShiftLog::STATUS_FLAGGED, $shift->fresh()->status);

        // Revert shift back to completed
        $statusResponse = $this->actingAs($admin)
            ->postJson(route('shifts.status', ['shift' => $shift->id]), [
                'status' => ShiftLog::STATUS_COMPLETED,
            ]);

        $statusResponse->assertOk();
        $this->assertEquals(ShiftLog::STATUS_COMPLETED, $shift->fresh()->status);
    }

    public function test_admin_can_adjust_shift_mileage_range(): void
    {
        $admin = User::create([
            'name' => 'Admin Dispatcher',
            'email' => 'admin-' . uniqid() . '@lancotri.local',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $bus = Bus::create([
            'bus_number' => '48',
            'plate' => 'TX-9028',
            'status' => 'standby',
            'odometer' => 125100,
        ]);

        $shift = ShiftLog::create([
            'shift_number' => 'SHF-0010',
            'bus_id' => $bus->id,
            'start_km' => 125000,
            'end_km' => 125100,
            'distance_km' => 100.0,
            'status' => ShiftLog::STATUS_COMPLETED,
            'started_at' => now()->subHours(2),
            'ended_at' => now(),
        ]);

        $response = $this->actingAs($admin)
            ->postJson(route('shifts.mileage', ['shift' => $shift->id]), [
                'start_km' => 125000,
                'end_km' => 125120,
                'reason' => 'Corrected OCR misread of final zero',
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'distance_km' => 120,
            'shift' => [
                'start_km' => 125000,
                'end_km' => 125120,
                'distance_km' => 120,
            ],
        ]);

        $freshShift = $shift->fresh();
        $this->assertEquals(125120, $freshShift->end_km);
        $this->assertEquals(120.0, $freshShift->distance_km);
        $this->assertEquals(125120, $bus->fresh()->odometer);
    }

    public function test_admin_can_assign_bus_service_route(): void
    {
        $admin = User::create([
            'name' => 'Admin Dispatcher',
            'email' => 'admin-' . uniqid() . '@lancotri.local',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $bus = Bus::create([
            'bus_number' => '50',
            'plate' => 'TX-9030',
            'status' => 'standby',
            'route' => null,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('buses.route', ['bus' => $bus->id]), [
                'route' => 'Route 101 - Metro Express',
            ]);

        $response->assertRedirect(route('buses'));
        $this->assertEquals('Route 101 - Metro Express', $bus->fresh()->route);
    }
}
