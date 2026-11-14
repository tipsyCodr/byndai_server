<?php

namespace App\Services\Flyer;

use App\Models\BrandKit;
use App\Models\MasterUserDetail;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FlyerCompositionService
{
    protected ?string $fontBold = null;
    protected ?string $fontRegular = null;
    protected ?string $fontMedium = null;

    public function __construct()
    {
        $this->resolveFonts();
    }

    /**
     * Locate available TrueType fonts
     */
    protected function resolveFonts(): void
    {
        $candidatesBold = [
            resource_path('fonts/Poppins-Bold.ttf'),
            resource_path('fonts/Inter-Bold.ttf'),
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            'C:\\Windows\\Fonts\\arialbd.ttf',
            'C:\\Windows\\Fonts\\arial.ttf',
        ];

        $candidatesRegular = [
            resource_path('fonts/Poppins-Regular.ttf'),
            resource_path('fonts/Inter-Regular.ttf'),
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            'C:\\Windows\\Fonts\\arial.ttf',
        ];

        $candidatesMedium = [
            resource_path('fonts/Poppins-Medium.ttf'),
            resource_path('fonts/Poppins-SemiBold.ttf'),
            resource_path('fonts/Inter-Medium.ttf'),
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            'C:\\Windows\\Fonts\\arial.ttf',
        ];

        foreach ($candidatesBold as $path) {
            if (file_exists($path)) {
                $this->fontBold = $path;
                break;
            }
        }

        foreach ($candidatesRegular as $path) {
            if (file_exists($path)) {
                $this->fontRegular = $path;
                break;
            }
        }

        foreach ($candidatesMedium as $path) {
            if (file_exists($path)) {
                $this->fontMedium = $path;
                break;
            }
        }
    }

    /**
     * Compose business branding and contact footer onto the raw flyer
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

        // Draw modern bottom branded footer bar (Top box is completely omitted to let AI art shine)
        $this->drawBottomBrandedFooter($image, $width, $height, $branding);

        // Output composed image
        $composedFileName = 'flyers/composed/' . Str::uuid() . '.png';
        ob_start();
        imagepng($image, null, 8);
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

        $showName = $options['name'] ?? $options['show_name'] ?? ($brandKit ? $brandKit->show_name : true);
        $showLogo = $options['logo'] ?? $options['show_logo'] ?? ($brandKit ? $brandKit->show_logo : true);
        $showContact = $options['phone'] ?? $options['show_contact'] ?? ($brandKit ? $brandKit->show_contact : true);
        $showWhatsApp = $options['whatsapp'] ?? $options['show_whatsapp'] ?? ($brandKit ? $brandKit->show_whatsapp : true);
        $showWebsite = $options['website'] ?? $options['show_website'] ?? ($brandKit ? $brandKit->show_website : true);
        $showAddress = $options['address'] ?? $options['show_address'] ?? ($brandKit ? $brandKit->show_address : true);

        $businessName = $showName
            ? ($brandKit?->business_name ?: $userDetail?->fname ?: $user->name ?: 'Our Business')
            : null;

        $logoPath = null;
        if ($showLogo) {
            $logoCandidate = $brandKit?->logo_path ?: $userDetail?->profile_logo;
            if ($logoCandidate && Storage::disk('public')->exists($logoCandidate)) {
                $logoPath = Storage::disk('public')->path($logoCandidate);
            }
        }

        $contact = $showContact
            ? ($brandKit?->contact_number ?: $userDetail?->mobile_one ?: $user->mobile)
            : null;

        $whatsapp = $showWhatsApp
            ? ($brandKit?->whatsapp_number ?: $contact)
            : null;

        $website = $showWebsite
            ? ($brandKit?->website)
            : null;

        $address = $showAddress
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
        ];
    }

    /**
     * Draw sleek glassmorphic bottom branded footer bar with TrueType typography
     */
    protected function drawBottomBrandedFooter($image, int $width, int $height, array $branding): void
    {
        $footerHeight = (int) ($height * 0.13); // 13% of height
        $footerY = $height - $footerHeight;

        $primaryRgb = $branding['primary_rgb'];
        $secondaryRgb = $branding['secondary_rgb'];

        // 1. Soft Gradient Fade from transparent into deep backdrop
        $gradientStartY = $height - (int) ($footerHeight * 1.35);
        $steps = 40;
        for ($i = 0; $i < $steps; $i++) {
            $currentY = (int) ($gradientStartY + ($i * (($height - $gradientStartY) / $steps)));
            $nextY = (int) ($gradientStartY + (($i + 1) * (($height - $gradientStartY) / $steps)));
            // Alpha: from 127 (100% transparent) down to 20 (rich solid glass)
            $alpha = (int) (127 - ($i / $steps) * 107);
            $gradColor = imagecolorallocatealpha($image, 10, 15, 30, $alpha);
            imagefilledrectangle($image, 0, $currentY, $width, $nextY, $gradColor);
        }

        // 2. Modern accent trim line (top of footer)
        $accentColor = imagecolorallocatealpha($image, $primaryRgb[0], $primaryRgb[1], $primaryRgb[2], 20);
        imagefilledrectangle($image, 0, $footerY, $width, $footerY + 3, $accentColor);

        $curX = (int) ($width * 0.04);
        $centerY = $footerY + (int) ($footerHeight / 2);

        // 3. Overlay Logo inside a clean white rounded card
        $logoSize = (int) ($footerHeight * 0.70);
        if (!empty($branding['logo_path']) && file_exists($branding['logo_path'])) {
            $logoResource = @imagecreatefromstring(file_get_contents($branding['logo_path']));
            if ($logoResource) {
                imagepalettetotruecolor($logoResource);
                imagealphablending($logoResource, true);
                imagesavealpha($logoResource, true);

                $origW = imagesx($logoResource);
                $origH = imagesy($logoResource);

                // Rounded White Logo Container
                $logoBg = imagecolorallocatealpha($image, 255, 255, 255, 5); // nearly solid white
                $logoBorder = imagecolorallocatealpha($image, 255, 255, 255, 40);
                $logoY = $centerY - (int) ($logoSize / 2);

                $this->imageFilledRoundedRect($image, $curX, $logoY, $curX + $logoSize, $logoY + $logoSize, 14, $logoBg);
                $this->imageRoundedRect($image, $curX, $logoY, $curX + $logoSize, $logoY + $logoSize, 14, $logoBorder);

                $pad = 6;
                imagecopyresampled(
                    $image,
                    $logoResource,
                    $curX + $pad,
                    $logoY + $pad,
                    0,
                    0,
                    $logoSize - ($pad * 2),
                    $logoSize - ($pad * 2),
                    $origW,
                    $origH
                );
                imagedestroy($logoResource);
                $curX += $logoSize + 20;
            }
        }

        // 4. Contact / WhatsApp Badge (Right Aligned)
        $contact = $branding['whatsapp'] ?: $branding['contact'];
        $pillWidth = 0;
        if (!empty($contact)) {
            $contactStr = $contact;
            $badgeFontSize = max(13, (int) ($width * 0.016));
            $pillHeight = (int) ($footerHeight * 0.38);

            // Calculate text width
            if ($this->fontBold) {
                $bbox = imagettfbbox($badgeFontSize, 0, $this->fontBold, $contactStr);
                $textW = abs($bbox[2] - $bbox[0]);
            } else {
                $textW = strlen($contactStr) * 9;
            }

            $pillWidth = $textW + 40;
            $pillX = $width - (int) ($width * 0.04) - $pillWidth;
            $pillY = $centerY - (int) ($pillHeight / 2);

            // WhatsApp Badge with brand secondary or WhatsApp green
            $badgeBg = imagecolorallocatealpha($image, $secondaryRgb[0], $secondaryRgb[1], $secondaryRgb[2], 0);
            $this->imageFilledRoundedRect($image, $pillX, $pillY, $pillX + $pillWidth, $pillY + $pillHeight, 12, $badgeBg);

            $badgeTextColor = imagecolorallocate($image, 255, 255, 255);
            $badgeTextX = $pillX + 20;
            $badgeTextY = $centerY + (int) ($badgeFontSize / 2.6);

            $this->drawText($image, $badgeFontSize, $badgeTextX, $badgeTextY, $contactStr, $badgeTextColor, $this->fontBold);
        }

        // 5. Draw Business Name & Address / Details
        $availableTextWidth = $width - $curX - $pillWidth - (int) ($width * 0.08);

        $nameFontSize = max(18, (int) ($width * 0.024)); // ~25pt on 1024px
        $nameColor = imagecolorallocate($image, 255, 255, 255);
        $nameY = $centerY - 6;

        if (!empty($branding['name'])) {
            $bizName = $this->truncateToFit($branding['name'], $nameFontSize, $availableTextWidth, $this->fontBold);
            $this->drawText($image, $nameFontSize, $curX, $nameY, $bizName, $nameColor, $this->fontBold);
        }

        // Subtitle (Address / Website)
        $detailText = '';
        if (!empty($branding['address'])) {
            $detailText = $branding['address'];
        } elseif (!empty($branding['website'])) {
            $detailText = $branding['website'];
        }

        if (!empty($detailText)) {
            $subFontSize = max(12, (int) ($width * 0.014)); // ~14pt on 1024px
            $subColor = imagecolorallocate($image, 203, 213, 225);
            $subY = $nameY + (int) ($subFontSize * 1.6);
            $detailText = $this->truncateToFit($detailText, $subFontSize, $availableTextWidth, $this->fontRegular);
            $this->drawText($image, $subFontSize, $curX, $subY, $detailText, $subColor, $this->fontRegular);
        }
    }

    /**
     * Render text with TrueType font if available, fallback to imagestring
     */
    protected function drawText($image, int $fontSize, int $x, int $y, string $text, int $color, ?string $fontFile): void
    {
        if ($fontFile && file_exists($fontFile) && function_exists('imagettftext')) {
            imagettftext($image, $fontSize, 0, $x, $y, $color, $fontFile, $text);
        } else {
            // Fallback to built-in bitmap font
            $fontNum = $fontSize >= 18 ? 5 : ($fontSize >= 14 ? 4 : 3);
            imagestring($image, $fontNum, $x, $y - 12, $text, $color);
        }
    }

    /**
     * Truncate text to fit into pixel width
     */
    protected function truncateToFit(string $text, int $fontSize, int $maxWidth, ?string $fontFile): string
    {
        if (!$fontFile || !file_exists($fontFile) || !function_exists('imagettfbbox')) {
            return mb_strimwidth($text, 0, 36, '...');
        }

        $bbox = imagettfbbox($fontSize, 0, $fontFile, $text);
        $w = abs($bbox[2] - $bbox[0]);
        if ($w <= $maxWidth) {
            return $text;
        }

        while (mb_strlen($text) > 3 && $w > $maxWidth) {
            $text = mb_substr($text, 0, -2);
            $bbox = imagettfbbox($fontSize, 0, $fontFile, $text . '...');
            $w = abs($bbox[2] - $bbox[0]);
        }

        return $text . '...';
    }

    /**
     * Draw filled rounded rectangle
     */
    protected function imageFilledRoundedRect($image, int $x1, int $y1, int $x2, int $y2, int $radius, int $color): void
    {
        imagefilledrectangle($image, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
        imagefilledrectangle($image, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);

        imagefilledellipse($image, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($image, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($image, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($image, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
    }

    /**
     * Draw rounded rectangle outline
     */
    protected function imageRoundedRect($image, int $x1, int $y1, int $x2, int $y2, int $radius, int $color): void
    {
        imageline($image, $x1 + $radius, $y1, $x2 - $radius, $y1, $color);
        imageline($image, $x1 + $radius, $y2, $x2 - $radius, $y2, $color);
        imageline($image, $x1, $y1 + $radius, $x1, $y2 - $radius, $color);
        imageline($image, $x2, $y1 + $radius, $x2, $y2 - $radius, $color);

        imagearc($image, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, 180, 270, $color);
        imagearc($image, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, 270, 360, $color);
        imagearc($image, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, 0, 90, $color);
        imagearc($image, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, 90, 180, $color);
    }

    protected function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if (strlen($hex) != 6) {
            return [31, 81, 229];
        }
        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }
}
