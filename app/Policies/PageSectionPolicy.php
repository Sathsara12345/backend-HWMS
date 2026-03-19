<?php

namespace App\Policies;

use App\Models\PageSection;
use App\Models\User;

class PageSectionPolicy
{
    private function ownsHotel(User $user, int $hotelId): bool
    {
        if (!$hotelId) return false;

        // Super admin can access everything
        if ($user->hasRole('super-admin')) return true;

        // Check if the user's hotel matches the requested hotel
        return (int) $user->hotel?->id === (int) $hotelId;
    }

    public function viewAny(User $user, int $hotelId): bool
    {
        return $this->ownsHotel($user, $hotelId);
    }

    public function view(User $user, PageSection $section): bool
    {
        return $this->ownsHotel($user, $section->hotel_id);
    }

    public function create(User $user, int $hotelId): bool
    {
        return $this->ownsHotel($user, $hotelId);
    }

    public function update(User $user, PageSection $section): bool
    {
        return $this->ownsHotel($user, $section->hotel_id);
    }

    public function delete(User $user, PageSection $section): bool
    {
        return $this->ownsHotel($user, $section->hotel_id);
    }
}
