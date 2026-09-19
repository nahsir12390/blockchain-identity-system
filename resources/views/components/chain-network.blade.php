<svg viewBox="0 0 480 280" class="chain-art" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Connected blocks representing an identity proof ledger">
    <g stroke="#365265" stroke-width="1"><path d="M20 210 240 80 460 210M20 150 240 20 460 150M20 270 240 140 460 270M80 245 300 115M180 270 400 140M80 115 300 245M180 90 400 220"/></g>
    <path d="m100 165 140-80 140 80-140 80Z" stroke="#6ee7b7" stroke-width="2" stroke-dasharray="5 7"/>
    @foreach ([[100,145],[240,65],[380,145],[240,225]] as [$x,$y])
        <g transform="translate({{ $x }} {{ $y }})" class="chain-node">
            <path d="M0-30 32-12 0 7-32-12Z" fill="#1b574e" stroke="#6ee7b7"/>
            <path d="M-32-12 0 7V43L-32 24Z" fill="#102b32" stroke="#6ee7b7"/>
            <path d="M32-12 0 7V43L32 24Z" fill="#153f40" stroke="#6ee7b7"/>
            <circle cy="-11" r="4" fill="#a7f3d0"/>
        </g>
    @endforeach
</svg>
