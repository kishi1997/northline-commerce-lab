import sharp from 'sharp';
import { mkdir } from 'node:fs/promises';
import { join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const projectRoot = resolve(fileURLToPath(new URL('../', import.meta.url)));
const outputDirectory = join(
  projectRoot,
  'wp-content',
  'plugins',
  'northline-commerce-rules',
  'assets',
  'demo-products',
);

await mkdir(outputDirectory, { recursive: true });

const products = [
  { file: 'nl-core-20.webp', index: '01', title: 'CORE 2.0', art: coreDevice() },
  { file: 'nl-field-11.webp', index: '02', title: 'FIELD 11', art: fieldModule() },
  { file: 'nl-studio-v.webp', index: '03', title: 'STUDIO V', art: studioSystem() },
  { file: 'nl-pocket-04.webp', index: '04', title: 'POCKET 04', art: pocketControl() },
  { file: 'nl-glass-01.webp', index: '05', title: 'GLASS 01', art: glassAttachment() },
  { file: 'nl-case-02.webp', index: '06', title: 'CASE 02', art: carryCase() },
  { file: 'nl-stand-03.webp', index: '07', title: 'STAND 03', art: displayStand() },
  { file: 'nl-ring-10.webp', index: '08', title: 'RING 10', art: ringSet() },
  { file: 'nl-screen-20.webp', index: '09', title: 'SCREEN 20', art: screenPack() },
  { file: 'nl-adapter-08.webp', index: '10', title: 'ADAPTER 08', art: adapter() },
  { file: 'nl-care-01.webp', index: '11', title: 'CARE 01', art: careKit() },
  { file: 'nl-brush-02.webp', index: '12', title: 'BRUSH 02', art: brushPair() },
];

for (const product of products) {
  const svg = productCard(product);
  await sharp(Buffer.from(svg)).webp({ quality: 90, effort: 5 }).toFile(join(outputDirectory, product.file));
  console.log(`built ${product.file}`);
}

function productCard({ index, title, art }) {
  return `
  <svg xmlns="http://www.w3.org/2000/svg" width="1200" height="1200" viewBox="0 0 1200 1200">
    <defs>
      <linearGradient id="paper" x1="0" x2="1" y1="0" y2="1">
        <stop offset="0" stop-color="#f3f0e9"/>
        <stop offset="1" stop-color="#cdc8be"/>
      </linearGradient>
      <linearGradient id="black" x1="0" x2="1">
        <stop offset="0" stop-color="#050606"/>
        <stop offset="0.52" stop-color="#2b2e2f"/>
        <stop offset="1" stop-color="#090a0a"/>
      </linearGradient>
      <linearGradient id="glass" x1="0" x2="1">
        <stop offset="0" stop-color="#ffffff" stop-opacity="0.24"/>
        <stop offset="0.55" stop-color="#ffffff" stop-opacity="0.68"/>
        <stop offset="1" stop-color="#777b79" stop-opacity="0.15"/>
      </linearGradient>
      <pattern id="grid" width="80" height="80" patternUnits="userSpaceOnUse">
        <path d="M80 0H0V80" fill="none" stroke="#171a1b" stroke-opacity="0.10" stroke-width="2"/>
      </pattern>
      <filter id="shadow" x="-50%" y="-50%" width="200%" height="200%">
        <feGaussianBlur in="SourceAlpha" stdDeviation="24"/>
        <feOffset dy="32"/>
        <feColorMatrix values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 .32 0"/>
        <feMerge><feMergeNode/><feMergeNode in="SourceGraphic"/></feMerge>
      </filter>
    </defs>
    <rect width="1200" height="1200" fill="url(#paper)"/>
    <rect width="1200" height="1200" fill="url(#grid)"/>
    <path d="M0 958H1200" stroke="#171a1b" stroke-opacity="0.45" stroke-width="2"/>
    <text x="56" y="88" fill="#171a1b" font-family="Arial, sans-serif" font-size="27" font-weight="700" letter-spacing="5">NORTHLINE / ${index}</text>
    <text x="56" y="1138" fill="#171a1b" font-family="Arial Narrow, Arial, sans-serif" font-size="46" font-weight="900" letter-spacing="2">${title}</text>
    <rect x="1020" y="52" width="124" height="12" fill="#963817"/>
    <g filter="url(#shadow)">${art}</g>
  </svg>`;
}

function coreDevice() {
  return `<ellipse cx="620" cy="932" rx="250" ry="45" fill="#171a1b" opacity="0.2"/>
    <rect x="430" y="240" width="380" height="690" rx="118" fill="url(#black)" stroke="#656968" stroke-width="3"/>
    <path d="M475 310H765M470 340H770M472 370H768" stroke="#747877" stroke-width="9" stroke-dasharray="12 13"/>
    <rect x="515" y="485" width="210" height="165" rx="12" fill="#050606" stroke="#777" stroke-width="3"/>
    <text x="620" y="568" text-anchor="middle" fill="#f2efe8" font-family="monospace" font-size="52">200</text>
    <text x="620" y="616" text-anchor="middle" fill="#f2efe8" font-family="monospace" font-size="25">200°C</text>
    <circle cx="620" cy="735" r="38" fill="#131515" stroke="#707473" stroke-width="4"/>`;
}

function fieldModule() {
  return `<ellipse cx="620" cy="932" rx="210" ry="42" fill="#171a1b" opacity="0.2"/>
    <rect x="450" y="350" width="340" height="575" rx="80" fill="url(#black)"/>
    <path d="M490 390V860M525 390V860M560 390V860M595 390V860M630 390V860M665 390V860M700 390V860M735 390V860" stroke="#4c5050" stroke-width="9"/>
    <rect x="470" y="300" width="300" height="92" rx="42" fill="#0b0c0c" stroke="#666" stroke-width="3"/>`;
}

function studioSystem() {
  return `<ellipse cx="610" cy="932" rx="330" ry="42" fill="#171a1b" opacity="0.2"/>
    <rect x="300" y="490" width="330" height="430" rx="70" fill="url(#black)"/>
    <rect x="530" y="285" width="350" height="635" rx="100" fill="url(#black)" stroke="#656968" stroke-width="3"/>
    <rect x="620" y="530" width="170" height="130" fill="#050606" stroke="#777" stroke-width="3"/>
    <text x="705" y="608" text-anchor="middle" fill="#f2efe8" font-family="monospace" font-size="42">V / 03</text>
    <path d="M580 350H830" stroke="#777" stroke-width="12" stroke-dasharray="8 12"/>`;
}

function pocketControl() {
  return `<ellipse cx="620" cy="925" rx="190" ry="40" fill="#171a1b" opacity="0.2"/>
    <rect x="445" y="440" width="350" height="480" rx="88" fill="url(#black)"/>
    <rect x="520" y="565" width="200" height="115" rx="10" fill="#050606" stroke="#767a79" stroke-width="3"/>
    <circle cx="620" cy="770" r="44" fill="#171a1b" stroke="#757978" stroke-width="5"/>
    <path d="M500 490H740" stroke="#656968" stroke-width="10" stroke-dasharray="9 13"/>`;
}

function glassAttachment() {
  return `<ellipse cx="620" cy="930" rx="250" ry="42" fill="#171a1b" opacity="0.2"/>
    <path d="M430 850L500 300Q510 245 565 245H675Q730 245 740 300L810 850Z" fill="url(#glass)" stroke="#777d7c" stroke-width="8"/>
    <rect x="390" y="815" width="460" height="112" rx="48" fill="url(#black)"/>
    <path d="M565 285H675M472 720H768" stroke="#fff" stroke-opacity="0.55" stroke-width="8"/>`;
}

function carryCase() {
  return `<ellipse cx="620" cy="925" rx="330" ry="45" fill="#171a1b" opacity="0.2"/>
    <path d="M500 370V310Q500 250 560 250H680Q740 250 740 310V370" fill="none" stroke="#1b1e1f" stroke-width="38"/>
    <rect x="280" y="360" width="680" height="560" rx="110" fill="url(#black)" stroke="#555958" stroke-width="5"/>
    <path d="M300 635H940" stroke="#777" stroke-opacity="0.55" stroke-width="4"/>
    <rect x="570" y="605" width="100" height="62" rx="12" fill="#111" stroke="#777" stroke-width="4"/>`;
}

function displayStand() {
  return `<ellipse cx="620" cy="930" rx="300" ry="50" fill="#171a1b" opacity="0.2"/>
    <path d="M370 820L470 480H770L870 820Z" fill="url(#black)"/>
    <ellipse cx="620" cy="485" rx="150" ry="45" fill="#343738" stroke="#777" stroke-width="4"/>
    <rect x="300" y="810" width="640" height="112" rx="20" fill="#101212"/>
    <path d="M415 800L530 525M825 800L710 525" stroke="#565a59" stroke-width="5"/>`;
}

function ringSet() {
  return `<ellipse cx="620" cy="925" rx="320" ry="45" fill="#171a1b" opacity="0.18"/>
    <g fill="none" stroke="url(#black)" stroke-width="56">
      <ellipse cx="410" cy="680" rx="160" ry="105"/>
      <ellipse cx="690" cy="520" rx="210" ry="135"/>
      <ellipse cx="800" cy="790" rx="125" ry="82"/>
    </g>
    <g fill="none" stroke="#6d7170" stroke-width="5">
      <ellipse cx="410" cy="680" rx="160" ry="105"/><ellipse cx="690" cy="520" rx="210" ry="135"/><ellipse cx="800" cy="790" rx="125" ry="82"/>
    </g>`;
}

function screenPack() {
  return `<ellipse cx="620" cy="925" rx="330" ry="45" fill="#171a1b" opacity="0.18"/>
    <g fill="#202323" stroke="#717574" stroke-width="6">
      <ellipse cx="420" cy="690" rx="175" ry="110"/><ellipse cx="690" cy="510" rx="210" ry="135"/><ellipse cx="810" cy="785" rx="145" ry="95"/>
    </g>
    <g fill="none" stroke="#8d918f" stroke-opacity="0.55" stroke-width="3">
      <path d="M280 690H560M420 590V790M510 410L870 610M690 375V645M690 680L930 860M810 690V880"/>
    </g>`;
}

function adapter() {
  return `<ellipse cx="620" cy="925" rx="300" ry="45" fill="#171a1b" opacity="0.2"/>
    <g transform="rotate(-16 620 630)">
      <rect x="250" y="505" width="700" height="250" rx="110" fill="url(#black)" stroke="#656968" stroke-width="5"/>
      <rect x="670" y="530" width="250" height="200" rx="70" fill="#171a1b"/>
      <path d="M720 550V710M760 540V720M800 540V720M840 550V710" stroke="#555958" stroke-width="10"/>
      <rect x="205" y="560" width="150" height="140" rx="40" fill="#363a3a"/>
    </g>`;
}

function careKit() {
  return `<ellipse cx="620" cy="925" rx="360" ry="45" fill="#171a1b" opacity="0.18"/>
    <rect x="190" y="400" width="820" height="510" rx="42" fill="#77736c" stroke="#343636" stroke-width="6"/>
    <g stroke="url(#black)" stroke-linecap="round">
      <path d="M330 760L540 520" stroke-width="32"/><path d="M500 800L680 490" stroke-width="22"/><path d="M690 810L850 515" stroke-width="28"/>
    </g>
    <circle cx="545" cy="515" r="60" fill="#1b1e1f"/><path d="M510 475L580 555M580 475L510 555" stroke="#777" stroke-width="9"/>`;
}

function brushPair() {
  return `<ellipse cx="620" cy="925" rx="330" ry="45" fill="#171a1b" opacity="0.18"/>
    <g transform="rotate(-22 620 620)">
      <rect x="250" y="560" width="650" height="48" rx="24" fill="url(#black)"/>
      <path d="M880 520L1040 584L880 648Z" fill="#151717"/>
      <path d="M890 530L1020 584L890 638" stroke="#707473" stroke-width="5" stroke-dasharray="8 7"/>
    </g>
    <g transform="rotate(18 620 690)">
      <rect x="270" y="690" width="610" height="42" rx="21" fill="url(#black)"/>
      <path d="M860 650L1015 711L860 772Z" fill="#202323"/>
      <path d="M870 660L995 711L870 762" stroke="#777" stroke-width="5" stroke-dasharray="8 7"/>
    </g>`;
}

