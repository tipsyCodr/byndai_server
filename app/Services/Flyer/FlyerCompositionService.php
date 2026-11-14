<?php

namespace App\Services\Flyer;

use App\Models\BrandKit;
use App\Models\MasterUserDetail;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FlyerCompositionService
{
    /**
     * Compose business branding, header greeting, and contact footer onto the raw flyer
     *
     * @return string Composed relative storage path
     */
    public function compose(
        string $rawImagePath,
        User $user,
        ?BrandKit $brandKit,
        string $headline,
        string $subheadline,
        array $brandingOptions = []
    ): string {
        $rawDisk = Storage::disk('public');
        if (!$rawDisk->exists($rawImagePath)) {
            throw new \Exception("Raw image file does not exist at {$rawImagePath}");
        }

        $rawFullPath = $rawDisk->path($rawImagePath);

        // Load background image
        $image = @imagecreatefromstring($rawDisk->get($rawImagePath));
        if (!$image) {
            throw new \Exception("Failed to decode raw AI image via GD.");
        }

        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);

        $width = imagesx($image);
        $height = imagesy($image);

        // Resolve branding info
        $branding = $this->resolveBrandingData($user, $brandKit, $brandingOptions);

        // 1. Draw top headline & greeting banner
        $this->drawTopGreetingHeader($image, $width, $height, $headline, $subheadline, $branding);

        // 2. Draw bottom branded footer bar
        $this->drawBottomBrandedFooter($image, $width, $height, $branding);

        // Output composed image
        $composedFileName = 'flyers/composed/' . Str::uuid() . '.png';
        ob_start();
        imagepng($image, null, 8); // PNG compression 8
        $outputData = ob_get_clean();
        imagedestroy($image);

        $rawDisk->put($composedFileName, $outputData);

        return $composedFileName;
    }

    /**
     * Resolve branding data combining BrandKit, MasterUserDetail and User
     */
    protected function resolveBrandingData(User $user, ?BrandKit $brandKit, array $options): array
    {
        $userDetail = MasterUserDetail::where('user_id', $user->id)->first();

        $businessName = $options['show_name'] ?? ($brandKit ? $brandKit->show_name : true)
            ? ($brandKit?->business_name ?: $userDetail?->fname ?: $user->name ?: 'Our Business')
            : null;

        $logoPath = null;
        if ($options['show_logo'] ?? ($brandKit ? $brandKit->show_logo : true)) {
            $logoCandidate = $brandKit?->logo_path ?: $userDetail?->profile_logo;
            if ($logoCandidate && Storage::disk('public')->exists($logoCandidate)) {
                $logoPath = Storage::disk('public')->path($logoCandidate);
            }
        }

        $contact = $options['show_contact'] ?? ($brandKit ? $brandKit->show_contact : true)
            ? ($brandKit?->contact_number ?: $userDetail?->mobile_one ?: $user->mobile)
            : null;

        $whatsapp = $options['show_whatsapp'] ?? ($brandKit ? $brandKit->show_whatsapp : true)
            ? ($brandKit?->whatsapp_number ?: $contact)
            : null;

        $website = $options['show_website'] ?? ($brandKit ? $brandKit->show_website : true)
            ? ($brandKit?->website)
            : null;

        $address = $options['show_address'] ?? ($brandKit ? $brandKit->show_address : true)
            ? ($brandKit?->address ?: $userDetail?->address)
            : null;

        $primaryColorHex = $brandKit?->primary_color ?: '#1F51E5';
        $secondaryColorHex = $brandKit?->secondary_color ?: '#25D366';

        return [
            'name' => $businessName,
            'logo_path' => $logoPath,
            'contact' => $contact,
            'whatsapp' => $whatsapp,
            'website' => $website,
            'address' => $address,
            'primary_rgb' => $this->hexToRgb($primaryColorHex),
            'secondary_rgb' => $this->hexToRgb($secondaryColorHex),
            'footer_style' => $brandKit?->footer_style ?? 0,
        ];
    }

    /**
     * Draw stylish top greeting header banner
     */
    protected function drawTopGreetingHeader($image, int $width, int $height, string $headline, string $subheadline, array $branding): void
    {
        if (empty($headline)) return;

        $headerHeight = (int) ($height * 0.12);
        $headerMarginTop = (int) ($height * 0.03);
        $headerWidth = (int) ($width * 0.90);
        $headerX = (int) (($width - $headerWidth) / 2);
        $headerY = $headerMarginTop;

        // Semi-transparent dark glass pill
        $glassBg = imagecolorallocatealpha($image, 15, 23, 42, 45); // ~82% opacity
        $glassBorder = imagecolorallocatealpha($image, 255, 255, 255, 95);
        $goldAccent = imagecolorallocatealpha($image, 251, 191, 36, 10);
        $whiteText = imagecolorallocate($image, 255, 255, 255);
        $subText = imagecolorallocate($image, 226, 232, 240);

        // Draw pill background
        $this->imageFilledRoundedRect($image, $headerX, $headerY, $headerX + $headerWidth, $headerY + $headerHeight, 22, $glassBg);
        $this->imageRoundedRect($image, $headerX, $headerY, $headerX + $headerWidth, $headerY + $headerHeight, 22, $glassBorder);

        // Golden accent bar on left of pill
        $this->imageFilledRoundedRect($image, $headerX + 16, $headerY + 14, $headerX + 22, $headerY + $headerHeight - 14, 3, $goldAccent);

        // Draw Headline
        $headlineFontSize = max(4, (int) ($width / 260)); // built-in font size 4 or 5
        $textX = $headerX + 34;
        $headlineY = $headerY + (int) ($headerHeight * 0.22);
        imagestring($image, 5, $textX, $headlineY, $this->truncateText($headline, 48), $whiteText);

        // Draw Subheadline
        if (!empty($subheadline)) {
            $subheadlineY = $headlineY + 24;
            imagestring($image, 3, $textX, $subheadlineY, $this->truncateText($subheadline, 72), $subText);
        }
    }

    /**
     * Draw bottom branded footer bar with logo, contact, whatsapp, address, and website
     */
    protected function drawBottomBrandedFooter($image, int $width, int $height, array $branding): void
    {
        $footerHeight = (int) ($height * 0.15);
        $footerY = $height - $footerHeight;

        $primaryRgb = $branding['primary_rgb'];
        $secondaryRgb = $branding['secondary_rgb'];

        // Dark rich container background with primary accent trim
        $footerBg = imagecolorallocatealpha($image, 15, 23, 42, 15); // Solid deep navy
        $accentLine = imagecolorallocate($image, $primaryRgb[0], $primaryRgb[1], $primaryRgb[2]);
        $whiteColor = imagecolorallocate($image, 255, 255, 255);
        $mutedColor = imagecolorallocate($image, 203, 213, 225);
        $badgeBg = imagecolorallocate($image, 37, 211, 102); // WhatsApp Green
        $badgeText = imagecolorallocate($image, 255, 255, 255);

        // Draw Footer Base
        imagefilledrectangle($image, 0, $footerY, $width, $height, $footerBg);

        // Draw top accent line (4px)
        imagefilledrectangle($image, 0, $footerY, $width, $footerY + 4, $accentLine);

        $curX = (int) ($width * 0.04);
        $centerY = $footerY + (int) ($footerHeight / 2);

        // Overlay Logo if exists
        $logoWidth = (int) ($footerHeight * 0.72);
        $logoHeight = $logoWidth;
        if (!empty($branding['logo_path']) && file_exists($branding['logo_path'])) {
            $logoResource = @imagecreatefromstring(file_get_contents($branding['logo_path']));
            if ($logoResource) {
                imagepalettetotruecolor($logoResource);
                imagealphablending($logoResource, true);
                imagesavealpha($logoResource, true);

                $origW = imagesx($logoResource);
                $origH = imagesy($logoResource);

                // Draw logo container circle / rounded rect
                $logoBg = imagecolorallocatealpha($image, 255, 255, 255, 10);
                $logoY = $centerY - (int) ($logoHeight / 2);
                $this->imageFilledRoundedRect($image, $curX, $logoY, $curX + $logoWidth, $logoY + $logoHeight, 14, $logoBg);

                imagecopyresampled(
                    $image,
                    $logoResource,
                    $curX + 6,
                    $logoY + 6,
                    0,
                    0,
                    $logoWidth - 12,
                    $logoHeight - 12,
                    $origW,
                    $origH
                );
                imagedestroy($logoResource);
                $curX += $logoWidth + 18;
            }
        }

        // Draw Business Name & Address / Details
        $nameY = $footerY + (int) ($footerHeight * 0.22);
        if (!empty($branding['name'])) {
            imagestring($image, 5, $curX, $nameY, $this->truncateText($branding['name'], 32), $whiteColor);
        }

        // Address or Website underneath business name
        $detailText = '';
        if (!empty($branding['address'])) {
            $detailText = $branding['address'];
        } elseif (!empty($branding['website'])) {
            $detailText = $branding['website'];
        }
        if (!empty($detailText)) {
            imagestring($image, 3, $curX, $nameY + 24, $this->truncateText($detailText, 45), $mutedColor);
        }

        // Right side: Phone & WhatsApp Contact Pill
        $contact = $branding['whatsapp'] ?: $branding['contact'];
        if (!empty($contact)) {
            $contactStr = "Call / WA: " . $contact;
            $pillWidth = (strlen($contactStr) * 9) + 24;
            $pillHeight = 36;
            $pillX = $width - (int) ($width * 0.04) - $pillWidth;
            $pillY = $centerY - (int) ($pillHeight / 2);

            // WhatsApp Badge
            $this->imageFilledRoundedRect($image, $pillX, $pillY, $pillX + $pillWidth, $pillY + $pillHeight, 10, $badgeBg);
            imagestring($image, 4, $pillX + 12, $pillY + 10, $contactStr, $badgeText);
        }
    }

    /**
     * Draw filled rounded rectangle using GD
     */
    protected function imageFilledRoundedRect($image, int $x1, int $y1, int $x2, int $y2, int $radius, $color): void
    {
        imagefilledrectangle($image, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
        imagefilledrectangle($image, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);

        imagefilledellipse($image, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($image, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($image, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($image, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
    }

    /**
     * Draw rounded rectangle border
     */
    protected function imageRoundedRect($image, int $x1, int $y1, int $x2, int $y2, int $radius, $color): void
    {
        imageline($image, $x1 + $radius, $y1, $x2 - $radius, $y1, $color);
        imageline($image, $x1 + $radius, $y2, $x2 - $radius, $y2, $color);
        imageline($image, $x1, $y1 + $radius, $x1, $y2 - $radius, $color);
        imageline($image, $x2, $y1 + $radius, $x2, $y2 - $radius, $color);

        imagearc($image, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, 180, 270, $color);
        imagearc($image, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, 270, 360, $color);
        imagearc($image, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, 90, 180, $color);
        imagearc($image, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, 0, 90, $color);
    }

    protected function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if (strlen($hex) !== 6) {
            return [31, 81, 229]; // Default royal blue
        }
        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    protected function truncateText(string $text, int $max): string
    {
        return strlen($text) > $max ? substr($text, 0, $max - 3) . '...' : $text;
    }
}

