@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
@if (trim($slot) === 'Laravel')
<span style="font-size: 28px; font-weight: 900; color: #38bdf8; letter-spacing: -0.5px;">🎧 Aurora<span style="color: #f8fafc;">Stream</span></span>
@else
<span style="font-size: 28px; font-weight: 900; color: #38bdf8; letter-spacing: -0.5px;">🎧 Aurora<span style="color: #f8fafc;">Stream</span></span>
@endif
</a>
</td>
</tr>
