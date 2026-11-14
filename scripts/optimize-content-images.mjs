import { access, writeFile } from "node:fs/promises";
import path from "node:path";
import sharp from "sharp";

const root = path.resolve(import.meta.dirname, "..");
const assetsDir = path.join(root, "public", "assets");
const sources = [
  "img/pexels/hero-spartum-2026.jpg",
  "img/unsplash/zona-pesas.jpg",
  "img/unsplash/yoga-grupo.jpg",
  "img/pexels/clase-colectiva.jpg",
  "img/pexels/entrenamiento-personal.jpg",
  "img/pexels/entrenador-fuerza.jpg",
  "img/pexels/gimnasio-moderno.jpg",
  "img/pixabay/fisioterapia-deportiva.jpg",
];
const requestedWidths = [480, 960, 1600];
const manifest = {};

for (const source of sources) {
  const input = path.join(assetsDir, source);
  await access(input);
  const metadata = await sharp(input).metadata();
  const sourceWidth = metadata.width ?? 1600;
  const widths = [...new Set(requestedWidths.filter((width) => width <= sourceWidth).concat(sourceWidth))]
    .sort((a, b) => a - b);
  const extension = path.extname(input);
  const stem = input.slice(0, -extension.length);
  const relativeStem = source.slice(0, -path.extname(source).length);
  manifest[source] = { width: sourceWidth, variants: { avif: {}, webp: {} } };

  for (const width of widths) {
    const avifPath = `${stem}-${width}.avif`;
    const webpPath = `${stem}-${width}.webp`;
    await sharp(input).resize({ width, withoutEnlargement: true }).avif({ quality: 52, effort: 5 }).toFile(avifPath);
    await sharp(input).resize({ width, withoutEnlargement: true }).webp({ quality: 80, effort: 5 }).toFile(webpPath);
    manifest[source].variants.avif[width] = `${relativeStem}-${width}.avif`;
    manifest[source].variants.webp[width] = `${relativeStem}-${width}.webp`;
  }
}

await writeFile(
  path.join(assetsDir, "img", "responsive-manifest.json"),
  `${JSON.stringify(manifest, null, 2)}\n`,
  "utf8",
);

console.log(`Optimized ${sources.length} source images.`);
