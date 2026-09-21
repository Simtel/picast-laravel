/**
 * Таблица именованных цветов CSS Color Module Level 4 в порядке спецификации.
 * Ключи — в нижнем регистре, значения — 6-значный hex в нижнем регистре.
 * `transparent` намеренно отсутствует: это ключевое слово прозрачности, а не цвет.
 *
 * Порядок записей значим: обратная таблица hex → название строится по правилу
 * «первое объявление выигрывает», поэтому дубликаты (#00ffff, #ff00ff, #808080)
 * разрешаются в пользу имён, объявленных раньше алиасов (aqua, fuchsia, gray).
 */
export const CSS_COLOR_NAMES = {
    aliceblue: '#f0f8ff',
    antiquewhite: '#faebd7',
    aqua: '#00ffff',
    aquamarine: '#7fffd4',
    azure: '#f0ffff',
    beige: '#f5f5dc',
    bisque: '#ffe4c4',
    black: '#000000',
    blanchedalmond: '#ffebcd',
    blue: '#0000ff',
    blueviolet: '#8a2be2',
    brown: '#a52a2a',
    burlywood: '#deb887',
    cadetblue: '#5f9ea0',
    chartreuse: '#7fff00',
    chocolate: '#d2691e',
    coral: '#ff7f50',
    cornflowerblue: '#6495ed',
    cornsilk: '#fff8dc',
    crimson: '#dc143c',
    cyan: '#00ffff',
    darkblue: '#00008b',
    darkcyan: '#008b8b',
    darkgoldenrod: '#b8860b',
    darkgray: '#a9a9a9',
    darkgreen: '#006400',
    darkgrey: '#a9a9a9',
    darkkhaki: '#bdb76b',
    darkmagenta: '#8b008b',
    darkolivegreen: '#556b2f',
    darkorange: '#ff8c00',
    darkorchid: '#9932cc',
    darkred: '#8b0000',
    darksalmon: '#e9967a',
    darkseagreen: '#8fbc8f',
    darkslateblue: '#483d8b',
    darkslategray: '#2f4f4f',
    darkslategrey: '#2f4f4f',
    darkturquoise: '#00ced1',
    darkviolet: '#9400d3',
    deeppink: '#ff1493',
    deepskyblue: '#00bfff',
    dimgray: '#696969',
    dimgrey: '#696969',
    dodgerblue: '#1e90ff',
    firebrick: '#b22222',
    floralwhite: '#fffaf0',
    forestgreen: '#228b22',
    fuchsia: '#ff00ff',
    gainsboro: '#dcdcdc',
    ghostwhite: '#f8f8ff',
    gold: '#ffd700',
    goldenrod: '#daa520',
    gray: '#808080',
    green: '#008000',
    greenyellow: '#adff2f',
    grey: '#808080',
    honeydew: '#f0fff0',
    hotpink: '#ff69b4',
    indianred: '#cd5c5c',
    indigo: '#4b0082',
    ivory: '#fffff0',
    khaki: '#f0e68c',
    lavender: '#e6e6fa',
    lavenderblush: '#fff0f5',
    lawngreen: '#7cfc00',
    lemonchiffon: '#fffacd',
    lightblue: '#add8e6',
    lightcoral: '#f08080',
    lightcyan: '#e0ffff',
    lightgoldenrodyellow: '#fafad2',
    lightgray: '#d3d3d3',
    lightgreen: '#90ee90',
    lightgrey: '#d3d3d3',
    lightpink: '#ffb6c1',
    lightsalmon: '#ffa07a',
    lightseagreen: '#20b2aa',
    lightskyblue: '#87cefa',
    lightslategray: '#778899',
    lightslategrey: '#778899',
    lightsteelblue: '#b0c4de',
    lightyellow: '#ffffe0',
    lime: '#00ff00',
    limegreen: '#32cd32',
    linen: '#faf0e6',
    magenta: '#ff00ff',
    maroon: '#800000',
    mediumaquamarine: '#66cdaa',
    mediumblue: '#0000cd',
    mediumorchid: '#ba55d3',
    mediumpurple: '#9370db',
    mediumseagreen: '#3cb371',
    mediumslateblue: '#7b68ee',
    mediumspringgreen: '#00fa9a',
    mediumturquoise: '#48d1cc',
    mediumvioletred: '#c71585',
    midnightblue: '#191970',
    mintcream: '#f5fffa',
    mistyrose: '#ffe4e1',
    moccasin: '#ffe4b5',
    navajowhite: '#ffdead',
    navy: '#000080',
    oldlace: '#fdf5e6',
    olive: '#808000',
    olivedrab: '#6b8e23',
    orange: '#ffa500',
    orangered: '#ff4500',
    orchid: '#da70d6',
    palegoldenrod: '#eee8aa',
    palegreen: '#98fb98',
    paleturquoise: '#afeeee',
    palevioletred: '#db7093',
    papayawhip: '#ffefd5',
    peachpuff: '#ffdab9',
    peru: '#cd853f',
    pink: '#ffc0cb',
    plum: '#dda0dd',
    powderblue: '#b0e0e6',
    purple: '#800080',
    rebeccapurple: '#663399',
    red: '#ff0000',
    rosybrown: '#bc8f8f',
    royalblue: '#4169e1',
    saddlebrown: '#8b4513',
    salmon: '#fa8072',
    sandybrown: '#f4a460',
    seagreen: '#2e8b57',
    seashell: '#fff5ee',
    sienna: '#a0522d',
    silver: '#c0c0c0',
    skyblue: '#87ceeb',
    slateblue: '#6a5acd',
    slategray: '#708090',
    slategrey: '#708090',
    snow: '#fffafa',
    springgreen: '#00ff7f',
    steelblue: '#4682b4',
    tan: '#d2b48c',
    teal: '#008080',
    thistle: '#d8bfd8',
    tomato: '#ff6347',
    turquoise: '#40e0d0',
    violet: '#ee82ee',
    wheat: '#f5deb3',
    white: '#ffffff',
    whitesmoke: '#f5f5f5',
    yellow: '#ffff00',
    yellowgreen: '#9acd32',
}

/** Фактическое число именованных цветов в таблице (инвариант проверяется тестом). */
export const NAMED_COLOR_COUNT = 148

const HEX_PATTERN = /^#?([0-9a-f]{6}|[0-9a-f]{3})$/i
const RGB_COMMA_PATTERN = /^rgb\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)$/i
const RGB_SPACE_PATTERN = /^rgb\(\s*(\d+)\s+(\d+)\s+(\d+)\s*\)$/i
const HSL_PATTERN = /^hsl\(\s*(-?\d+(?:\.\d+)?)(?:deg)?[\s,]+(-?\d+(?:\.\d+)?)%?[\s,]+(-?\d+(?:\.\d+)?)%?\s*\)$/i

/** Обратная таблица hex → название. Строится один раз; первое объявление выигрывает. */
const hexToName = new Map()
for (const [name, hex] of Object.entries(CSS_COLOR_NAMES)) {
    if (!hexToName.has(hex)) {
        hexToName.set(hex, name)
    }
}

/** Разбирает hex (3 или 6 цифр, с `#` или без) в { r, g, b } либо null. */
function parseHexString(value) {
    const match = value.match(HEX_PATTERN)
    if (match === null) {
        return null
    }

    const digits = match[1].length === 3
        ? match[1].split('').map((digit) => digit + digit).join('')
        : match[1]

    return {
        r: Number.parseInt(digits.slice(0, 2), 16),
        g: Number.parseInt(digits.slice(2, 4), 16),
        b: Number.parseInt(digits.slice(4, 6), 16),
    }
}

/**
 * Разбирает цвет в { r, g, b } (целые 0–255) либо null.
 * Поддерживаются: 148 именованных цветов CSS, `rgb(r, g, b)`, `rgb(r g b)`,
 * `hsl(h, s%, l%)`, `hsl(h s l)` (проценты необязательны, `h` — любое число,
 * нормализуется по модулю 360) и hex `#rgb` / `#rrggbb` (с `#` или без).
 * Альфа-канал не поддерживается: `rgba()`, `/`-нотация, 4- и 8-значный hex,
 * `transparent`, `currentcolor` дают null.
 */
export function parseColor(input) {
    const value = String(input ?? '').trim()
    if (value === '') {
        return null
    }

    const key = value.toLowerCase()
    if (Object.hasOwn(CSS_COLOR_NAMES, key)) {
        return parseHexString(CSS_COLOR_NAMES[key])
    }

    const rgbMatch = value.match(RGB_COMMA_PATTERN) ?? value.match(RGB_SPACE_PATTERN)
    if (rgbMatch !== null) {
        const r = Number(rgbMatch[1])
        const g = Number(rgbMatch[2])
        const b = Number(rgbMatch[3])

        return [r, g, b].every((channel) => channel >= 0 && channel <= 255) ? { r, g, b } : null
    }

    const hslMatch = value.match(HSL_PATTERN)
    if (hslMatch !== null) {
        const h = Number(hslMatch[1])
        const s = Number(hslMatch[2])
        const l = Number(hslMatch[3])

        return s >= 0 && s <= 100 && l >= 0 && l <= 100 ? hslToRgb(h, s, l) : null
    }

    return parseHexString(value)
}

/**
 * Переводит RGB (целые 0–255) в { h, s, l }: h — градусы 0–360,
 * s и l — проценты 0–100 (дробные, округление делает toHslString).
 * Ахроматические цвета (r === g === b) дают { h: 0, s: 0, l }.
 */
export function rgbToHsl(r, g, b) {
    const red = r / 255
    const green = g / 255
    const blue = b / 255

    const max = Math.max(red, green, blue)
    const min = Math.min(red, green, blue)
    const l = (max + min) / 2
    const delta = max - min

    if (delta === 0) {
        return { h: 0, s: 0, l: l * 100 }
    }

    const s = l > 0.5 ? delta / (2 - max - min) : delta / (max + min)

    let h
    if (max === red) {
        h = ((green - blue) / delta) % 6
    } else if (max === green) {
        h = (blue - red) / delta + 2
    } else {
        h = (red - green) / delta + 4
    }

    h *= 60
    if (h < 0) {
        h += 360
    }

    return { h, s: s * 100, l: l * 100 }
}

function hueToChannel(p, q, t) {
    let hue = t
    if (hue < 0) {
        hue += 1
    }
    if (hue > 1) {
        hue -= 1
    }
    if (hue < 1 / 6) {
        return p + (q - p) * 6 * hue
    }
    if (hue < 1 / 2) {
        return q
    }
    if (hue < 2 / 3) {
        return p + (q - p) * (2 / 3 - hue) * 6
    }

    return p
}

/**
 * Переводит HSL (h — градусы, любые; s и l — проценты 0–100) в { r, g, b }
 * с целыми каналами 0–255. h нормализуется по модулю 360.
 */
export function hslToRgb(h, s, l) {
    const hue = ((h % 360) + 360) % 360 / 360
    const saturation = s / 100
    const lightness = l / 100

    if (saturation === 0) {
        const gray = Math.round(lightness * 255)

        return { r: gray, g: gray, b: gray }
    }

    const q = lightness < 0.5
        ? lightness * (1 + saturation)
        : lightness + saturation - lightness * saturation
    const p = 2 * lightness - q

    return {
        r: Math.round(hueToChannel(p, q, hue + 1 / 3) * 255),
        g: Math.round(hueToChannel(p, q, hue) * 255),
        b: Math.round(hueToChannel(p, q, hue - 1 / 3) * 255),
    }
}

/** Строка `#rrggbb` в нижнем регистре. */
export function toHex(color) {
    return '#' + [color.r, color.g, color.b]
        .map((channel) => channel.toString(16).padStart(2, '0'))
        .join('')
}

/** Строка вида `rgb(51, 102, 204)`. */
export function toRgbString(color) {
    return `rgb(${color.r}, ${color.g}, ${color.b})`
}

/** Строка вида `hsl(220, 60%, 50%)`: h — целые градусы, s и l — целые проценты. */
export function toHslString(color) {
    const { h, s, l } = rgbToHsl(color.r, color.g, color.b)

    return `hsl(${Math.round(h)}, ${Math.round(s)}%, ${Math.round(l)}%)`
}

/** Точное название цвета CSS либо null, если совпадения нет. */
export function toCssName(color) {
    return hexToName.get(toHex(color)) ?? null
}

/** Единая точка для DOM-слоя: все четыре представления цвета сразу. */
export function describeColor(color) {
    return {
        hex: toHex(color),
        rgb: toRgbString(color),
        hsl: toHslString(color),
        name: toCssName(color),
    }
}
