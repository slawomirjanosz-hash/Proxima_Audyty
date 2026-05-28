<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $startKey = '<!-- ═══ KOSZTY DOJAZDU';
        $endKey   = '<!-- ═══ WARUNKI';

        // Clean offer_templates
        foreach (DB::table('offer_templates')->whereNotNull('html_content')->cursor() as $row) {
            $html = $this->strip($row->html_content, $startKey, $endKey);
            if ($html !== $row->html_content) {
                DB::table('offer_templates')->where('id', $row->id)->update(['html_content' => $html]);
            }
        }

        // Clean offers (rendered html_content)
        foreach (DB::table('offers')->whereNotNull('html_content')->cursor() as $row) {
            $html = $this->strip($row->html_content, $startKey, $endKey);
            if ($html !== $row->html_content) {
                DB::table('offers')->where('id', $row->id)->update(['html_content' => $html]);
            }
        }
    }

    private function strip(string $html, string $startKey, string $endKey): string
    {
        $sPos = strpos($html, $startKey);
        $ePos = $sPos !== false ? strpos($html, $endKey, $sPos) : false;
        if ($sPos !== false && $ePos !== false) {
            $html = substr($html, 0, $sPos) . substr($html, $ePos);
        }
        return $html;
    }

    public function down(): void {}
};
