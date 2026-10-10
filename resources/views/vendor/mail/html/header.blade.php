<tr>
    <td class="header" style="padding: 24px; border-bottom: 2px solid #0a0a0a; background-color: #ffffff; text-align: left;">
        <a href="{{ $url ?? config('app.url') }}" style="font-family: 'SF Mono', Monaco, Consolas, monospace; font-size: 13px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: #0a0a0a; text-decoration: none;">
            {{ $slot }}
        </a>
    </td>
</tr>