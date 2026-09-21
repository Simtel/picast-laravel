import { describe, expect, it } from 'vitest'
import {
    CSS_COLOR_NAMES,
    NAMED_COLOR_COUNT,
    describeColor,
    hslToRgb,
    parseColor,
    rgbToHsl,
    toCssName,
    toHex,
    toHslString,
    toRgbString,
} from './color-converter.js'

describe('parseColor', () => {
    it('parses hex with and without #, in any case', () => {
        expect(parseColor('#ff0000')).toEqual({ r: 255, g: 0, b: 0 })
        expect(parseColor('#f00')).toEqual({ r: 255, g: 0, b: 0 })
        expect(parseColor('FF0000')).toEqual({ r: 255, g: 0, b: 0 })
        expect(parseColor('ff0000')).toEqual({ r: 255, g: 0, b: 0 })
        expect(parseColor('  #3366cc  ')).toEqual({ r: 51, g: 102, b: 204 })
    })

    it('parses rgb() with commas and with spaces', () => {
        expect(parseColor('rgb(255, 0, 0)')).toEqual({ r: 255, g: 0, b: 0 })
        expect(parseColor('rgb(255 0 0)')).toEqual({ r: 255, g: 0, b: 0 })
        expect(parseColor('RGB(51,102,204)')).toEqual({ r: 51, g: 102, b: 204 })
    })

    it('parses hsl() with and without percent signs', () => {
        expect(parseColor('hsl(0, 100%, 50%)')).toEqual({ r: 255, g: 0, b: 0 })
        expect(parseColor('hsl(0 100 50)')).toEqual({ r: 255, g: 0, b: 0 })
        expect(parseColor('hsl(360deg, 100%, 50%)')).toEqual({ r: 255, g: 0, b: 0 })
        expect(parseColor('hsl(-120, 100%, 50%)')).toEqual({ r: 0, g: 0, b: 255 })
    })

    it('parses named colors case-insensitively', () => {
        expect(parseColor('red')).toEqual({ r: 255, g: 0, b: 0 })
        expect(parseColor('Red')).toEqual({ r: 255, g: 0, b: 0 })
        expect(parseColor('RED')).toEqual({ r: 255, g: 0, b: 0 })
        expect(parseColor('rebeccapurple')).toEqual({ r: 102, g: 51, b: 153 })
    })

    it('returns null for empty and unparseable input', () => {
        expect(parseColor('')).toBeNull()
        expect(parseColor('   ')).toBeNull()
        expect(parseColor(null)).toBeNull()
        expect(parseColor(undefined)).toBeNull()
        expect(parseColor('notacolor')).toBeNull()
        expect(parseColor('constructor')).toBeNull()
        expect(parseColor('toString')).toBeNull()
    })

    it('rejects out-of-range rgb and hsl components', () => {
        expect(parseColor('rgb(300, 0, 0)')).toBeNull()
        expect(parseColor('rgb(-1, 0, 0)')).toBeNull()
        expect(parseColor('hsl(0, 200%, 50%)')).toBeNull()
        expect(parseColor('hsl(0, 100%, 101%)')).toBeNull()
    })

    it('rejects the alpha channel and CSS-wide keywords', () => {
        expect(parseColor('#ff000080')).toBeNull()
        expect(parseColor('#ff0000ff')).toBeNull()
        expect(parseColor('#f00f')).toBeNull()
        expect(parseColor('rgba(255, 0, 0, 0.5)')).toBeNull()
        expect(parseColor('rgb(255 0 0 / 50%)')).toBeNull()
        expect(parseColor('hsl(0, 100%, 50%, 0.5)')).toBeNull()
        expect(parseColor('transparent')).toBeNull()
        expect(parseColor('currentcolor')).toBeNull()
    })
})

describe('hslToRgb', () => {
    it('converts primary hues', () => {
        expect(hslToRgb(0, 100, 50)).toEqual({ r: 255, g: 0, b: 0 })
        expect(hslToRgb(120, 100, 50)).toEqual({ r: 0, g: 255, b: 0 })
        expect(hslToRgb(240, 100, 50)).toEqual({ r: 0, g: 0, b: 255 })
    })

    it('normalizes hue modulo 360', () => {
        expect(hslToRgb(360, 100, 50)).toEqual({ r: 255, g: 0, b: 0 })
        expect(hslToRgb(-120, 100, 50)).toEqual({ r: 0, g: 0, b: 255 })
    })

    it('treats zero saturation as gray', () => {
        expect(hslToRgb(0, 0, 50)).toEqual({ r: 128, g: 128, b: 128 })
    })
})

describe('rgbToHsl', () => {
    it('converts primaries', () => {
        expect(rgbToHsl(255, 0, 0)).toEqual({ h: 0, s: 100, l: 50 })

        const green = rgbToHsl(0, 128, 0)
        expect(green.h).toBe(120)
        expect(green.s).toBe(100)
        expect(green.l).toBeCloseTo(25.098, 3)
    })

    it('treats achromatic colors as zero hue and saturation', () => {
        const gray = rgbToHsl(128, 128, 128)
        expect(gray.h).toBe(0)
        expect(gray.s).toBe(0)
        expect(gray.l).toBeCloseTo(50.196, 3)
    })
})

describe('formatters', () => {
    const RED = { r: 255, g: 0, b: 0 }
    const BLUE = { r: 51, g: 102, b: 204 }

    it('formats hex with zero padding', () => {
        expect(toHex(RED)).toBe('#ff0000')
        expect(toHex({ r: 0, g: 0, b: 0 })).toBe('#000000')
        expect(toHex(BLUE)).toBe('#3366cc')
        expect(toHex({ r: 1, g: 2, b: 3 })).toBe('#010203')
    })

    it('formats rgb()', () => {
        expect(toRgbString(RED)).toBe('rgb(255, 0, 0)')
        expect(toRgbString(BLUE)).toBe('rgb(51, 102, 204)')
    })

    it('formats hsl() with rounded h/s/l', () => {
        expect(toHslString(RED)).toBe('hsl(0, 100%, 50%)')
        expect(toHslString(BLUE)).toBe('hsl(220, 60%, 50%)')
        expect(toHslString({ r: 0, g: 0, b: 0 })).toBe('hsl(0, 0%, 0%)')
        expect(toHslString({ r: 255, g: 255, b: 255 })).toBe('hsl(0, 0%, 100%)')
        expect(toHslString({ r: 128, g: 128, b: 128 })).toBe('hsl(0, 0%, 50%)')
    })

    it('maps hex back to the first declared CSS name', () => {
        expect(toCssName(RED)).toBe('red')
        expect(toCssName({ r: 0, g: 255, b: 255 })).toBe('aqua')
        expect(toCssName({ r: 255, g: 0, b: 255 })).toBe('fuchsia')
        expect(toCssName({ r: 128, g: 128, b: 128 })).toBe('gray')
        expect(toCssName({ r: 102, g: 51, b: 153 })).toBe('rebeccapurple')
        expect(toCssName(BLUE)).toBeNull()
    })

    it('describes a color in all four formats at once', () => {
        expect(describeColor(BLUE)).toEqual({
            hex: '#3366cc',
            rgb: 'rgb(51, 102, 204)',
            hsl: 'hsl(220, 60%, 50%)',
            name: null,
        })
    })
})

describe('round-trip', () => {
    const SAMPLES = [
        { r: 255, g: 0, b: 0 },
        { r: 0, g: 128, b: 0 },
        { r: 0, g: 0, b: 255 },
        { r: 255, g: 255, b: 0 },
        { r: 0, g: 255, b: 255 },
        { r: 255, g: 0, b: 255 },
        { r: 192, g: 192, b: 192 },
        { r: 128, g: 128, b: 128 },
        { r: 128, g: 0, b: 0 },
        { r: 128, g: 128, b: 0 },
        { r: 0, g: 128, b: 128 },
        { r: 0, g: 0, b: 128 },
        { r: 128, g: 0, b: 128 },
        { r: 255, g: 165, b: 0 },
        { r: 255, g: 255, b: 255 },
        { r: 0, g: 0, b: 0 },
        { r: 102, g: 51, b: 153 },
        { r: 51, g: 102, b: 204 },
        { r: 127, g: 95, b: 63 },
    ]

    it('survives hex and rgb round-trips exactly', () => {
        for (const color of SAMPLES) {
            expect(parseColor(toHex(color))).toEqual(color)
            expect(parseColor(toRgbString(color))).toEqual(color)
        }
    })

    it('survives the hsl round-trip within a 3-per-channel tolerance', () => {
        for (const color of SAMPLES) {
            const back = parseColor(toHslString(color))
            expect(back).not.toBeNull()
            expect(Math.abs(back.r - color.r)).toBeLessThanOrEqual(3)
            expect(Math.abs(back.g - color.g)).toBeLessThanOrEqual(3)
            expect(Math.abs(back.b - color.b)).toBeLessThanOrEqual(3)
        }
    })

    it('parses every declared name and maps it back to a name', () => {
        for (const hex of Object.values(CSS_COLOR_NAMES)) {
            const color = parseColor(hex)
            expect(color).toEqual(parseColor(hex.slice(1)))
            expect(toCssName(color)).not.toBeNull()
        }
    })
})

describe('CSS_COLOR_NAMES invariants', () => {
    it('has exactly NAMED_COLOR_COUNT entries', () => {
        expect(Object.keys(CSS_COLOR_NAMES).length).toBe(NAMED_COLOR_COUNT)
        expect(NAMED_COLOR_COUNT).toBe(148)
    })

    it('uses lowercase names and 6-digit lowercase hex values', () => {
        for (const [name, hex] of Object.entries(CSS_COLOR_NAMES)) {
            expect(name).toBe(name.toLowerCase())
            expect(hex).toMatch(/^#[0-9a-f]{6}$/)
        }
    })

    it('excludes transparent and includes rebeccapurple', () => {
        expect(CSS_COLOR_NAMES.transparent).toBeUndefined()
        expect(CSS_COLOR_NAMES.rebeccapurple).toBe('#663399')
    })

    it('covers all 16 basic color keywords', () => {
        const basic = [
            'aqua', 'black', 'blue', 'fuchsia', 'gray', 'green', 'lime', 'maroon',
            'navy', 'olive', 'purple', 'red', 'silver', 'teal', 'white', 'yellow',
        ]

        for (const name of basic) {
            expect(CSS_COLOR_NAMES[name]).toBeDefined()
        }
    })
})
