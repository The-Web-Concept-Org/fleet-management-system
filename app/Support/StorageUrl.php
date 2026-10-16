<?php

namespace App\Support;

/**
 * Builds public URLs for files stored on the "public" disk.
 *
 * Storage::disk('public')->url() is derived from the static APP_URL env value,
 * so it produces the wrong host/scheme whenever the app is served from a
 * different domain than APP_URL (the classic case: production runs on
 * https://example.com while APP_URL is still http://localhost). The resulting
 * <img> tags then point at localhost, or load over http on an https page where
 * the browser blocks them as mixed content.
 *
 * This helper resolves the path against the current request instead, so the
 * scheme and host are always the ones the client actually used. No per-domain
 * configuration is needed, and switching domains (or adding a new one) just
 * works. The file itself is served by the public /storage/{path} route, or by
 * the public/storage symlink when it exists.
 */
class StorageUrl
{
    public static function for(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return url('storage/' . ltrim($path, '/'));
    }
}
