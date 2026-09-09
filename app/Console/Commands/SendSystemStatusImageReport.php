<?php

namespace App\Console\Commands;

use App\Http\Controllers\Settings\ApiController;
use App\Models\Beacon;
use App\Services\TelegramNotifier;
use App\Support\StatusThreshold;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SendSystemStatusImageReport extends Command
{
    /**
     * php artisan telegram:image-status-report
     */
    protected $signature = 'telegram:image-status-report';

    protected $description = 'Render a beacon/siren status report as an image and send it to Telegram';

    // Canvas / card chrome
    private const WIDTH         = 1000;
    private const OUTER_MARGIN  = 24;  // gray shell -> white card
    private const CARD_RADIUS   = 20;
    private const CARD_PADDING  = 34;  // white card -> inner content
    private const HEADER_HEIGHT = 96;

    private const COL_GAP          = 40;
    private const PIE_DIAMETER     = 150;
    private const MAX_OFFLINE_ROWS = 12;
    private const ROW_HEIGHT       = 26;

    // Edit this wording to taste — layout below reflows automatically to fit
    // however many lines it wraps to.
    private const REPORT_DESCRIPTION = 'This report summarizes the connectivity status of Emergency Warning System beacons and sirens across Muntinlupa City, based on the most recent status poll from field devices.';

    private const REPORT_DISCLAIMER = 'This is an automated, computer-generated report. Data reflects the latest status poll and may not capture changes in real time. For active incidents or urgent verification, confirm status directly via the monitoring dashboard or on-site inspection before taking action.';

    public function handle(ApiController $apiController, TelegramNotifier $telegram): int
    {
        if (! extension_loaded('gd')) {
            $this->error('The GD PHP extension is required to render the status report image.');
            return self::FAILURE;
        }

        $beaconCounts  = Beacon::getCounts();
        $beaconOnline  = $beaconCounts['online'];
        $beaconOffline = $beaconCounts['offline'];
        $beaconTotal   = $beaconOnline + $beaconOffline;

        $offlineBeacons = Beacon::where('status', false)
            ->orderBy('name')
            ->get(['name', 'last_seen_at']);

        $sirenCounts        = $apiController->getSirenStatusCounts();
        $sirenDataAvailable = $sirenCounts !== null;
        $sirenTotal         = $sirenCounts['total'] ?? 0;
        $sirenOffline       = $sirenCounts['offline'] ?? 0;

        $path = $this->renderImage(
            $beaconOffline,
            $beaconTotal,
            $offlineBeacons,
            $sirenOffline,
            $sirenTotal,
            $sirenDataAvailable
        );

        $sent = $telegram->sendPhoto($path, 'Muntinlupa Emergency Warning System — Status Report');

        @unlink($path);

        if (! $sent) {
            $this->error('Failed to send the status report image to Telegram.');
            return self::FAILURE;
        }

        $this->info('Status report image sent to Telegram.');
        return self::SUCCESS;
    }

    /**
     * @param \Illuminate\Support\Collection<int, Beacon> $offlineBeacons
     */
    private function renderImage(
        int $beaconOffline,
        int $beaconTotal,
        $offlineBeacons,
        int $sirenOffline,
        int $sirenTotal,
        bool $sirenDataAvailable
    ): string {
        $beaconLabel = StatusThreshold::label($beaconOffline, $beaconTotal);
        $sirenLabel  = $sirenDataAvailable ? StatusThreshold::label($sirenOffline, $sirenTotal) : 'Unknown';

        // Fonts are resolved before layout math because the description and
        // disclaimer paragraphs are word-wrapped up front (their line count
        // affects the canvas height), which needs font metrics to measure.
        $fontBold = $this->resolveFont(true);
        $fontReg  = $this->resolveFont(false);

        // --- Layout (computed up front so canvas height and drawing agree) ---
        $rowsShown = min($offlineBeacons->count(), self::MAX_OFFLINE_ROWS);
        $extraRows = max(0, $offlineBeacons->count() - self::MAX_OFFLINE_ROWS);
        $noteRows  = ($extraRows > 0 ? 1 : 0) + ($offlineBeacons->isEmpty() ? 1 : 0);

        $cardX  = self::OUTER_MARGIN;
        $cardW  = self::WIDTH - (2 * self::OUTER_MARGIN);
        $innerX = $cardX + self::CARD_PADDING;
        $innerW = $cardW - (2 * self::CARD_PADDING);

        $descLineHeight = 16;
        $descLines      = $this->wrapText($fontReg, 12, self::REPORT_DESCRIPTION, (int) $innerW);
        $descHeight     = count($descLines) * $descLineHeight;

        $disclaimerLineHeight = 13;
        $disclaimerLines      = $this->wrapText($fontReg, 9, self::REPORT_DISCLAIMER, (int) $innerW);
        $disclaimerHeight     = count($disclaimerLines) * $disclaimerLineHeight;

        $colWidth = ($innerW - self::COL_GAP) / 2;
        $leftX    = $innerX;
        $rightX   = $innerX + $colWidth + self::COL_GAP;

        $contentTopY = self::OUTER_MARGIN + self::HEADER_HEIGHT + self::CARD_PADDING;
        $descY       = $contentTopY;
        $statusRowY  = $descY + $descHeight + 18;
        $pieTopY     = $statusRowY + 46;
        $pieCy       = $pieTopY + (self::PIE_DIAMETER / 2);
        $captionY    = $pieTopY + self::PIE_DIAMETER + 22;

        $tableCardY   = $captionY + 46;
        $tableHeaderH = 40;
        $tableBodyH   = ($rowsShown + $noteRows) * self::ROW_HEIGHT;
        $tableCardH   = $tableHeaderH + $tableBodyH + 20;

        $disclaimerLabelY  = $tableCardY + $tableCardH + 26;
        $disclaimerTextY   = $disclaimerLabelY + 16;
        $disclaimerBottomY = $disclaimerTextY + $disclaimerHeight;

        $footerRuleY = $disclaimerBottomY + 16;
        $creditY     = $footerRuleY + 18;

        $cardH  = ($creditY - self::OUTER_MARGIN) + 24;
        $height = $cardH + (2 * self::OUTER_MARGIN);

        // --- Canvas + palette ---
        // Reuses the reds/greens already used elsewhere for status (critical/normal)
        // plus the blue already used for the report title, applied in a card-based
        // layout (rounded panels, status pills) matching the dashboard's UI.
        $img = imagecreatetruecolor(self::WIDTH, (int) $height);

        $palette = [
            'canvas'        => imagecolorallocate($img, 226, 229, 235),
            'card'          => imagecolorallocate($img, 255, 255, 255),
            'card_shadow'   => imagecolorallocate($img, 205, 209, 217),
            'header'        => imagecolorallocate($img, 30, 82, 122),
            'header_accent' => imagecolorallocate($img, 37, 150, 190),
            'text'          => imagecolorallocate($img, 31, 41, 55),
            'text_muted'    => imagecolorallocate($img, 107, 114, 128),
            'white'         => imagecolorallocate($img, 255, 255, 255),
            'white_dim'     => imagecolorallocate($img, 205, 224, 234),
            'red'           => imagecolorallocate($img, 220, 38, 38),
            'red_bg'        => imagecolorallocate($img, 254, 226, 226),
            'green'         => imagecolorallocate($img, 22, 163, 74),
            'green_bg'      => imagecolorallocate($img, 220, 245, 227),
            'gray_badge'    => imagecolorallocate($img, 130, 130, 130),
            'gray_badge_bg' => imagecolorallocate($img, 231, 233, 236),
            'border'        => imagecolorallocate($img, 228, 231, 236),
            'row_stripe'    => imagecolorallocate($img, 246, 248, 250),
        ];

        imagefill($img, 0, 0, $palette['canvas']);

        // --- Card shadow + body ---
        $this->roundRect($img, $cardX + 3, self::OUTER_MARGIN + 4, $cardW, (int) $cardH, self::CARD_RADIUS, $palette['card_shadow'], ['tl', 'tr', 'bl', 'br']);
        $this->roundRect($img, $cardX, self::OUTER_MARGIN, $cardW, (int) $cardH, self::CARD_RADIUS, $palette['card'], ['tl', 'tr', 'bl', 'br']);

        // --- Header bar (rounded top only) + accent strip ---
        $this->roundRect($img, $cardX, self::OUTER_MARGIN, $cardW, self::HEADER_HEIGHT, self::CARD_RADIUS, $palette['header'], ['tl', 'tr']);
        imagefilledrectangle($img, $cardX, self::OUTER_MARGIN + self::HEADER_HEIGHT - 4, $cardX + $cardW, self::OUTER_MARGIN + self::HEADER_HEIGHT - 1, $palette['header_accent']);

        $this->drawText($img, 'Muntinlupa Emergency Warning System', $innerX, self::OUTER_MARGIN + 26, $fontBold, 19, $palette['white']);
        $this->drawText($img, 'Automated Status Report', $innerX, self::OUTER_MARGIN + 52, $fontReg, 12, $palette['white_dim']);

        $now = now();
        $this->drawRightAlignedText($img, $now->format('F j, Y'), $cardX + $cardW - self::CARD_PADDING, self::OUTER_MARGIN + 30, $fontReg, 13, $palette['white']);
        $this->drawRightAlignedText($img, $now->format('g:i A'), $cardX + $cardW - self::CARD_PADDING, self::OUTER_MARGIN + 50, $fontReg, 13, $palette['white_dim']);

        // --- Description ---
        $this->drawWrappedText($img, $descLines, $innerX, (int) $descY, $descLineHeight, $fontReg, 12, $palette['text']);

        // --- Status row: label + colored pill per column ---
        $this->drawStatusPill($img, $leftX, $statusRowY, 'BEACON STATUS', $beaconLabel, $fontBold, $fontReg, $palette);
        $this->drawStatusPill($img, $rightX, $statusRowY, 'SIREN STATUS', $sirenLabel, $fontBold, $fontReg, $palette);

        // --- Divider between columns ---
        imageline($img, (int) ($innerX + $colWidth + self::COL_GAP / 2), (int) $pieTopY - 4, (int) ($innerX + $colWidth + self::COL_GAP / 2), (int) $captionY + 18, $palette['border']);

        // --- Pie charts ---
        $leftPieCx  = (int) ($leftX + ($colWidth / 2));
        $rightPieCx = (int) ($rightX + ($colWidth / 2));

        $this->drawPie($img, $leftPieCx, (int) $pieCy, self::PIE_DIAMETER, $beaconOffline, $beaconTotal, $palette['red'], $palette['green'], $palette['gray_badge_bg']);
        if ($sirenDataAvailable) {
            $this->drawPie($img, $rightPieCx, (int) $pieCy, self::PIE_DIAMETER, $sirenOffline, $sirenTotal, $palette['red'], $palette['green'], $palette['gray_badge_bg']);
        } else {
            imagefilledellipse($img, $rightPieCx, (int) $pieCy, self::PIE_DIAMETER, self::PIE_DIAMETER, $palette['gray_badge_bg']);
        }

        $this->drawCenteredText($img, "{$beaconOffline} / {$beaconTotal} offline", $leftPieCx, (int) $captionY, $fontBold, 13, $palette['text']);
        $this->drawCenteredText(
            $img,
            $sirenDataAvailable ? "{$sirenOffline} / {$sirenTotal} offline" : 'Data unavailable',
            $rightPieCx,
            (int) $captionY,
            $fontBold,
            13,
            $sirenDataAvailable ? $palette['text'] : $palette['text_muted']
        );
        // Always shown: siren counts are aggregate-only — individual siren
        // stations aren't tracked the way beacons are (see StatusThreshold usage above).
        $this->drawCenteredText($img, 'Per-station detail not yet available', $rightPieCx, (int) $captionY + 20, $fontReg, 10, $palette['text_muted']);

        // --- Offline beacon table (now spans the full card width) ---
        $tableCardW = (int) $innerW;
        $this->roundRect($img, $innerX, (int) $tableCardY, $tableCardW, (int) $tableCardH, 12, $palette['row_stripe'], ['tl', 'tr', 'bl', 'br']);
        $this->roundRect($img, $innerX, (int) $tableCardY, $tableCardW, $tableHeaderH, 12, $palette['border'], ['tl', 'tr']);

        $padX         = 18;
        $nameColX     = $innerX + $padX + 40;
        $lastSeenColX = $innerX + $tableCardW - $padX - 150;

        $this->drawText($img, 'LIST OF OFFLINE STATIONS', $innerX + $padX, (int) $tableCardY + 14, $fontBold, 11, $palette['text']);
        $headerRowY = $tableCardY + $tableHeaderH;
        $this->drawText($img, 'NO.', $innerX + $padX, (int) $headerRowY + 6, $fontReg, 10, $palette['text_muted']);
        $this->drawText($img, 'NAME', $nameColX, (int) $headerRowY + 6, $fontReg, 10, $palette['text_muted']);
        $this->drawText($img, 'LAST SEEN', $lastSeenColX, (int) $headerRowY + 6, $fontReg, 10, $palette['text_muted']);

        $rowY = $headerRowY + 20;
        $i = 1;
        foreach ($offlineBeacons->take(self::MAX_OFFLINE_ROWS) as $beacon) {
            if ($i % 2 === 0) {
                imagefilledrectangle($img, $innerX + 4, (int) $rowY - 4, $innerX + $tableCardW - 4, (int) $rowY + self::ROW_HEIGHT - 8, $palette['card']);
            }
            $lastSeen = $beacon->last_seen_at ? $beacon->last_seen_at->format('M j, g:i A') : 'Never';
            $this->drawText($img, (string) $i, $innerX + $padX, (int) $rowY, $fontReg, 11, $palette['text']);
            $this->drawText($img, Str::limit($beacon->name ?? 'Unnamed', 60), $nameColX, (int) $rowY, $fontReg, 11, $palette['text']);
            $this->drawText($img, $lastSeen, $lastSeenColX, (int) $rowY, $fontReg, 11, $palette['text']);
            $rowY += self::ROW_HEIGHT;
            $i++;
        }

        if ($extraRows > 0) {
            $this->drawText($img, "+ {$extraRows} more", $innerX + $padX, (int) $rowY, $fontReg, 11, $palette['text_muted']);
            $rowY += self::ROW_HEIGHT;
        }

        if ($offlineBeacons->isEmpty()) {
            $this->drawText($img, 'No offline beacons — all stations reporting.', $innerX + $padX, (int) $rowY, $fontReg, 11, $palette['text_muted']);
        }

        // --- Disclaimer ---
        $this->drawText($img, 'DISCLAIMER', $innerX, (int) $disclaimerLabelY, $fontBold, 10, $palette['text_muted']);
        $this->drawWrappedText($img, $disclaimerLines, $innerX, (int) $disclaimerTextY, $disclaimerLineHeight, $fontReg, 9, $palette['text_muted']);

        // --- Footer ---
        imageline($img, $cardX + self::CARD_PADDING, (int) $footerRuleY, $cardX + $cardW - self::CARD_PADDING, (int) $footerRuleY, $palette['border']);
        $this->drawCenteredText(
            $img,
            'Computer-generated report — developed by Uplink Integrated Solutions Inc.',
            self::WIDTH / 2,
            (int) $creditY,
            $fontReg,
            10,
            $palette['text_muted']
        );

        $dir = storage_path('app/telegram-reports');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $path = $dir . '/status-report-' . now()->format('Ymd-His') . '.png';
        imagepng($img, $path);
        imagedestroy($img);

        return $path;
    }

    /**
     * Draws an uppercase section label with a colored pill badge beneath it
     * (red/green/gray for Critical/Normal/Unknown), matching the status-chip
     * look used elsewhere in the dashboard UI.
     */
    private function drawStatusPill($img, float $x, float $y, string $label, string $statusLabel, ?string $fontBold, ?string $fontReg, array $palette): void
    {
        $this->drawText($img, $label, (int) $x, (int) $y, $fontBold, 12, $palette['text_muted']);

        [$fg, $bg] = match ($statusLabel) {
            'Critical' => [$palette['red'], $palette['red_bg']],
            'Normal'   => [$palette['green'], $palette['green_bg']],
            default    => [$palette['gray_badge'], $palette['gray_badge_bg']],
        };

        $badgeText = strtoupper($statusLabel);
        $badgeH    = 24;
        $badgeW    = $this->textWidth($fontBold, 11, $badgeText) + 28;
        $badgeX    = (int) $x;
        $badgeY    = (int) $y + 18;

        $this->roundRect($img, $badgeX, $badgeY, $badgeW, $badgeH, (int) ($badgeH / 2), $bg, ['tl', 'tr', 'bl', 'br']);
        $this->drawCenteredText($img, $badgeText, (int) ($badgeX + $badgeW / 2), $badgeY + 5, $fontBold, 11, $fg);
    }

    /**
     * Filled rectangle with selectively-rounded corners, e.g. ['tl','tr'] for a
     * rounded-top-only header bar. Built from two overlapping rectangles plus a
     * filled circle at each rounded corner (square fill at any corner left sharp).
     */
    private function roundRect($img, $x, $y, $w, $h, $r, int $color, array $corners): void
    {
        $x = (int) $x; $y = (int) $y; $w = (int) $w; $h = (int) $h; $r = (int) $r;

        imagefilledrectangle($img, $x + $r, $y, $x + $w - $r, $y + $h, $color);
        imagefilledrectangle($img, $x, $y + $r, $x + $w, $y + $h - $r, $color);

        $corner = fn ($cx, $cy) => imagefilledellipse($img, $cx, $cy, $r * 2, $r * 2, $color);

        in_array('tl', $corners, true) ? $corner($x + $r, $y + $r) : imagefilledrectangle($img, $x, $y, $x + $r, $y + $r, $color);
        in_array('tr', $corners, true) ? $corner($x + $w - $r, $y + $r) : imagefilledrectangle($img, $x + $w - $r, $y, $x + $w, $y + $r, $color);
        in_array('bl', $corners, true) ? $corner($x + $r, $y + $h - $r) : imagefilledrectangle($img, $x, $y + $h - $r, $x + $r, $y + $h, $color);
        in_array('br', $corners, true) ? $corner($x + $w - $r, $y + $h - $r) : imagefilledrectangle($img, $x + $w - $r, $y + $h - $r, $x + $w, $y + $h, $color);
    }

    /**
     * Looks for a bundled/system TTF font so text renders cleanly; falls back to
     * GD's built-in bitmap font (smaller, plainer, but needs zero setup) if none found.
     * Checked for both weights independently — drop matching .ttf files in
     * storage/fonts yourself for guaranteed results on any OS.
     */
    private function resolveFont(bool $bold): ?string
    {
        $candidates = $bold
            ? [
                storage_path('fonts/DejaVuSans-Bold.ttf'),
                'C:\\Windows\\Fonts\\calibrib.ttf',
                'C:\\Windows\\Fonts\\arialbd.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            ]
            : [
                storage_path('fonts/DejaVuSans.ttf'),
                'C:\\Windows\\Fonts\\calibri.ttf',
                'C:\\Windows\\Fonts\\arial.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function textWidth(?string $font, int $size, string $text): int
    {
        if ($font) {
            $box = imagettfbbox($size, 0, $font, $text);
            return (int) abs($box[2] - $box[0]);
        }

        return imagefontwidth($this->gdFontId($size)) * strlen($text);
    }

    private function drawText($img, string $text, int $x, int $y, ?string $font, int $size, int $color): void
    {
        if ($font) {
            imagettftext($img, $size, 0, $x, $y + $size, $color, $font, $text);
            return;
        }

        imagestring($img, $this->gdFontId($size), $x, $y, $text, $color);
    }

    private function drawCenteredText($img, string $text, int $centerX, int $y, ?string $font, int $size, int $color): void
    {
        $width = $this->textWidth($font, $size, $text);
        $this->drawText($img, $text, (int) ($centerX - $width / 2), $y, $font, $size, $color);
    }

    private function drawRightAlignedText($img, string $text, int $rightX, int $y, ?string $font, int $size, int $color): void
    {
        $width = $this->textWidth($font, $size, $text);
        $this->drawText($img, $text, (int) ($rightX - $width), $y, $font, $size, $color);
    }

    /** Greedy word-wrap: breaks $text into lines no wider than $maxWidth. */
    private function wrapText(?string $font, int $size, string $text, int $maxWidth): array
    {
        $words   = preg_split('/\s+/', trim($text));
        $lines   = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = $current === '' ? $word : $current . ' ' . $word;
            if ($current !== '' && $this->textWidth($font, $size, $candidate) > $maxWidth) {
                $lines[]  = $current;
                $current = $word;
            } else {
                $current = $candidate;
            }
        }
        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines;
    }

    private function drawWrappedText($img, array $lines, int $x, int $y, int $lineHeight, ?string $font, int $size, int $color): void
    {
        foreach ($lines as $index => $line) {
            $this->drawText($img, $line, $x, $y + ($index * $lineHeight), $font, $size, $color);
        }
    }

    private function gdFontId(int $size): int
    {
        return $size >= 15 ? 5 : ($size >= 12 ? 4 : 3);
    }

    /**
     * offline/total rendered as a red slice over a green base circle, starting at 12
     * o'clock. Drawing green-then-red-on-top avoids floating point angle math having
     * to add up to exactly 360 - much less fragile than two precise arcs.
     */
    private function drawPie($img, int $cx, int $cy, int $diameter, int $offline, int $total, int $red, int $green, int $empty): void
    {
        if ($total <= 0) {
            imagefilledellipse($img, $cx, $cy, $diameter, $diameter, $empty);
            return;
        }

        imagefilledellipse($img, $cx, $cy, $diameter, $diameter, $green);

        $offlineDegrees = ($offline / $total) * 360;

        if ($offlineDegrees <= 0.1) {
            return;
        }

        if ($offlineDegrees >= 359.9) {
            imagefilledellipse($img, $cx, $cy, $diameter, $diameter, $red);
            return;
        }

        $start = 270;
        $end = 270 + $offlineDegrees;

        if ($end <= 360) {
            imagefilledarc($img, $cx, $cy, $diameter, $diameter, $start, (int) round($end), $red, IMG_ARC_PIE);
        } else {
            imagefilledarc($img, $cx, $cy, $diameter, $diameter, $start, 360, $red, IMG_ARC_PIE);
            imagefilledarc($img, $cx, $cy, $diameter, $diameter, 0, (int) round($end - 360), $red, IMG_ARC_PIE);
        }
    }
}