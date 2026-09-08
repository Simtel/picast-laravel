import { describe, expect, it } from 'vitest'
import {
    dateDiff,
    formatIso,
    formatLocalString,
    formatRelative,
    formatUtcString,
    parseHumanDate,
    parseTimestampInput,
    startOf,
} from './timestamp-converter.js'

const S = 1000
const M = 60 * S
const H = 60 * M
const D = 24 * H

describe('parseTimestampInput', () => {
    it('traits 10-digit value as seconds', () => {
        expect(parseTimestampInput('1752000000')).toBe(1752000000 * S)
    })

    it('traits 13-digit value as milliseconds', () => {
        expect(parseTimestampInput('1752000000000')).toBe(1752000000000)
    })

    it('uses 1e12 as the seconds/milliseconds boundary', () => {
        expect(parseTimestampInput('999999999999')).toBe(999999999999 * S)
        expect(parseTimestampInput('1000000000000')).toBe(1e12)
    })

    it('accepts negative (pre-1970) timestamps', () => {
        expect(parseTimestampInput('-1000000000')).toBe(-1e12)
    })

    it('trims surrounding whitespace', () => {
        expect(parseTimestampInput('  1752000000  ')).toBe(1752000000 * S)
    })

    it('rejects non-numeric input', () => {
        expect(parseTimestampInput('abc')).toBeNull()
        expect(parseTimestampInput('')).toBeNull()
        expect(parseTimestampInput('1.5')).toBeNull()
        expect(parseTimestampInput('1752-000')).toBeNull()
    })

    it('rejects values beyond the JS Date range', () => {
        expect(parseTimestampInput('8640000000000000')).toBe(8640000000000000)
        expect(parseTimestampInput('8640000000000001')).toBeNull()
        expect(parseTimestampInput('99999999999999999999')).toBeNull()
    })
})

describe('parseHumanDate', () => {
    it('parses ISO 8601 with Z', () => {
        expect(parseHumanDate('2026-09-08T12:00:00.000Z', 'utc')).toBe(Date.UTC(2026, 8, 8, 12))
    })

    it('parses ISO 8601 with numeric offset', () => {
        expect(parseHumanDate('2026-09-08T12:00:00+03:00', 'utc')).toBe(Date.UTC(2026, 8, 8, 9))
    })

    it('parses YYYY-MM-DD with and without time', () => {
        expect(parseHumanDate('2026-09-08', 'utc')).toBe(Date.UTC(2026, 8, 8))
        expect(parseHumanDate('2026-09-08 12:00:00', 'utc')).toBe(Date.UTC(2026, 8, 8, 12))
        expect(parseHumanDate('2026-09-08 12:00', 'utc')).toBe(Date.UTC(2026, 8, 8, 12))
    })

    it('parses RU-style day.month.year formats', () => {
        expect(parseHumanDate('08.09.2026', 'utc')).toBe(Date.UTC(2026, 8, 8))
        expect(parseHumanDate('08/09/2026 12:00', 'utc')).toBe(Date.UTC(2026, 8, 8, 12))
        expect(parseHumanDate('08-09-2026 12:00:00', 'utc')).toBe(Date.UTC(2026, 8, 8, 12))
    })

    it('parses dates from the past and the future', () => {
        expect(parseHumanDate('2001-01-01', 'utc')).toBe(Date.UTC(2001, 0, 1))
        expect(parseHumanDate('2038-01-19 03:14:07', 'utc')).toBe(Date.UTC(2038, 0, 19, 3, 14, 7))
    })

    it('parses month-name strings as UTC wall clock when tz is utc', () => {
        expect(parseHumanDate('September 8, 2026', 'utc')).toBe(Date.UTC(2026, 8, 8))
        expect(parseHumanDate('8 September 2026', 'utc')).toBe(Date.UTC(2026, 8, 8))
    })

    it('respects an explicit zone in month-name strings', () => {
        expect(parseHumanDate('September 8, 2026 12:00:00 GMT', 'utc')).toBe(Date.UTC(2026, 8, 8, 12))
    })

    it('parses local wall-clock when tz is local', () => {
        expect(parseHumanDate('2026-09-08 12:00:00', 'local')).toBe(Date.UTC(2026, 8, 8, 12))
        expect(parseHumanDate('08.09.2026 00:00', 'local')).toBe(Date.UTC(2026, 8, 8))
    })

    it('rejects unparseable or impossible dates', () => {
        expect(parseHumanDate('')).toBeNull()
        expect(parseHumanDate('foo bar')).toBeNull()
        expect(parseHumanDate('2026-13-01', 'utc')).toBeNull()
        expect(parseHumanDate('2026-02-30', 'utc')).toBeNull()
        expect(parseHumanDate('32.13.2026', 'utc')).toBeNull()
    })
})

describe('formatters', () => {
    const ms = Date.UTC(2026, 8, 8, 12)

    it('formats ISO 8601, GMT and local strings', () => {
        expect(formatIso(ms)).toBe('2026-09-08T12:00:00.000Z')
        expect(formatUtcString(ms)).toBe('Tue, 08 Sep 2026 12:00:00 GMT')
        expect(formatLocalString(ms)).toContain('Sep 08 2026')
    })

    it('returns null for invalid timestamps', () => {
        expect(formatIso('not a number')).toBeNull()
        expect(formatUtcString(Number.NaN)).toBeNull()
        expect(formatLocalString(null)).toBeNull()
    })
})

describe('formatRelative', () => {
    const now = Date.UTC(2026, 8, 8, 12)

    it('returns "только что" for the same instant and tiny deltas', () => {
        expect(formatRelative(now, now)).toBe('только что')
        expect(formatRelative(now + 300, now)).toBe('только что')
    })

    it('formats the past and the future', () => {
        expect(formatRelative(now - 2 * H, now)).toBe('2 часа назад')
        expect(formatRelative(now + 5 * M, now)).toBe('через 5 минут')
        expect(formatRelative(now - 45 * S, now)).toBe('45 сек. назад')
        expect(formatRelative(now + 2 * D, now)).toBe('через 2 дня')
    })

    it('pluralizes correctly', () => {
        expect(formatRelative(now - M, now)).toBe('1 минуту назад')
        expect(formatRelative(now - 2 * M, now)).toBe('2 минуты назад')
        expect(formatRelative(now - 5 * M, now)).toBe('5 минут назад')
        expect(formatRelative(now - 21 * H, now)).toBe('21 час назад')
        expect(formatRelative(now - D, now)).toBe('1 день назад')
        expect(formatRelative(now - 400 * D, now)).toBe('1 год назад')
    })

    it('returns null for invalid timestamps', () => {
        expect(formatRelative('nope', now)).toBeNull()
    })
})

describe('startOf', () => {
    const nowMs = Date.UTC(2026, 8, 8, 14, 30, 45)

    it('computes start of day/month/year', () => {
        expect(startOf('day', 'utc', nowMs)).toBe(Date.UTC(2026, 8, 8))
        expect(startOf('month', 'utc', nowMs)).toBe(Date.UTC(2026, 8, 1))
        expect(startOf('year', 'utc', nowMs)).toBe(Date.UTC(2026, 0, 1))
    })

    it('works in local time', () => {
        expect(startOf('day', 'local', nowMs)).toBe(Date.UTC(2026, 8, 8))
        expect(startOf('month', 'local', nowMs)).toBe(Date.UTC(2026, 8, 1))
        expect(startOf('year', 'local', nowMs)).toBe(Date.UTC(2026, 0, 1))
    })

    it('returns null for invalid input', () => {
        expect(startOf('day', 'utc', Number.NaN)).toBeNull()
    })
})

describe('dateDiff', () => {
    it('splits a difference into days/hours/minutes/seconds', () => {
        const a = Date.UTC(2026, 8, 8)
        const b = a + D + 2 * H + 3 * M + 4 * S

        expect(dateDiff(a, b)).toEqual({ days: 1, hours: 2, minutes: 3, seconds: 4 })
        expect(dateDiff(b, a)).toEqual({ days: 1, hours: 2, minutes: 3, seconds: 4 })
    })

    it('keeps the parts consistent', () => {
        const a = Date.UTC(2020, 0, 1)
        const b = Date.UTC(2030, 5, 15)
        const { days, hours, minutes, seconds } = dateDiff(a, b)
        const totalSeconds = days * 86400 + hours * 3600 + minutes * 60 + seconds

        expect(totalSeconds).toBe(Math.round(Math.abs(b - a) / S))
    })

    it('returns null for invalid input', () => {
        expect(dateDiff(Number.NaN, 1)).toBeNull()
    })
})
