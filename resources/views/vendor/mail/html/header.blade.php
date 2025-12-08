<tr>
    <td class="header" style="padding: 25px 0;">
        <a href="{{ url('/') }}" style="display: inline-block; text-decoration: none;">
            {{-- Construct the full public URL explicitly --}}
            @php
                $logoUrl = config('app.url') . '/upload/uptm.png';
            @endphp
            
            <img 
                src="{{ $logoUrl }}" 
                alt="UPTM University Logo" 
                style="width: 100px; height: auto; max-width: 150px; display: block; margin: 0 auto;"
            >
            <span style="font-size: 16px; color: #1e40af; font-weight: bold; display: block; margin-top: 5px;">
                UPTM Academic AI Assistant
            </span>
        </a>
    </td>
</tr>