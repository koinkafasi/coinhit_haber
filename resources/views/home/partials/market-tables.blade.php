{{-- Ana sayfa piyasa verisi tabloları (Bölüm 7 modül 19, §15.1): her sekme kendi h2 + tablo --}}
<div class="market-data-widget" data-widget-type="market-tables">
    @foreach ($marketTables as $table)
        <x-market-table
            :title="$table['title']"
            :quotes="$table['quotes']"
            :href="$table['href']"
            :columns="$table['columns'] ?? null"
            :id="'market-table-'.$table['key']"
        />
    @endforeach
</div>