<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Replace hardcoded ENESA company data in offer template HTML with {{placeholders}}.
        $templates = \App\Models\OfferTemplate::whereNotNull('html_content')->get();

        foreach ($templates as $tpl) {
            $h = $tpl->html_content;

            // Replace hardcoded ENESA header address block (any whitespace variant)
            $h = preg_replace(
                '/<strong>ENESA Sp\. z o\.o\.<\/strong>.*?biuro@enesa\.pl \| \+48 22 123 45 67/s',
                "<strong>{{enesa_name}}</strong>\n    {{enesa_street}}<br>\n    {{enesa_postal}} {{enesa_city}}<br>\n    NIP: {{enesa_nip}}<br>\n    {{enesa_email}} | {{enesa_phone}}",
                $h
            );

            // Replace hardcoded ENESA footer line
            $h = str_replace(
                'ENESA Sp. z o.o. | ul. Energetyczna 15, 00-900 Warszawa',
                '{{enesa_name}} | {{enesa_street}}, {{enesa_postal}} {{enesa_city}}',
                $h
            );
            $h = str_replace('biuro@enesa.pl | www.enesa.pl', '{{enesa_email}} | www.enesa.pl', $h);

            if ($h !== $tpl->html_content) {
                $tpl->html_content = $h;
                $tpl->save();
            }
        }
    }

    public function down(): void
    {
        // Not reversible
    }
};
