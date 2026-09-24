import { describe, expect, it } from 'vitest'
import { diffLines, splitLines, summarize } from './text-diff.js'

describe('splitLines', () => {
    it('splits text into lines', () => {
        expect(splitLines('a\nb\nc')).toEqual(['a', 'b', 'c'])
    })

    it('normalizes CRLF and CR to LF', () => {
        expect(splitLines('a\r\nb\rc')).toEqual(['a', 'b', 'c'])
    })

    it('returns an empty array for empty or non-string input', () => {
        expect(splitLines('')).toEqual([])
        expect(splitLines(null)).toEqual([])
    })
})

describe('diffLines', () => {
    it('marks identical lines as equal', () => {
        expect(diffLines(['a', 'b'], ['a', 'b'])).toEqual([
            { type: 'equal', aLine: 1, bLine: 1, text: 'a' },
            { type: 'equal', aLine: 2, bLine: 2, text: 'b' },
        ])
    })

    it('detects an added line', () => {
        expect(diffLines(['a', 'c'], ['a', 'b', 'c'])).toEqual([
            { type: 'equal', aLine: 1, bLine: 1, text: 'a' },
            { type: 'added', bLine: 2, text: 'b' },
            { type: 'equal', aLine: 2, bLine: 3, text: 'c' },
        ])
    })

    it('detects a removed line', () => {
        expect(diffLines(['a', 'b', 'c'], ['a', 'c'])).toEqual([
            { type: 'equal', aLine: 1, bLine: 1, text: 'a' },
            { type: 'removed', aLine: 2, text: 'b' },
            { type: 'equal', aLine: 3, bLine: 2, text: 'c' },
        ])
    })

    it('reports a changed line as removed + added', () => {
        expect(diffLines(['a', 'b'], ['a', 'B'])).toEqual([
            { type: 'equal', aLine: 1, bLine: 1, text: 'a' },
            { type: 'removed', aLine: 2, text: 'b' },
            { type: 'added', bLine: 2, text: 'B' },
        ])
    })

    it('handles empty sides', () => {
        expect(diffLines([], ['x'])).toEqual([{ type: 'added', bLine: 1, text: 'x' }])
        expect(diffLines(['x'], [])).toEqual([{ type: 'removed', aLine: 1, text: 'x' }])
        expect(diffLines([], [])).toEqual([])
    })

    it('keeps duplicate lines aligned instead of shifting', () => {
        const changes = diffLines(['x', 'x', 'x'], ['x', 'x'])

        expect(summarize(changes)).toEqual({ added: 0, removed: 1, unchanged: 2, total: 1 })
    })
})

describe('summarize', () => {
    it('counts lines by type', () => {
        const changes = diffLines(['a', 'b', 'c'], ['a', 'B', 'c', 'd'])

        expect(summarize(changes)).toEqual({ added: 2, removed: 1, unchanged: 2, total: 3 })
    })

    it('returns zeroed counters for empty input', () => {
        expect(summarize([])).toEqual({ added: 0, removed: 0, unchanged: 0, total: 0 })
    })
})
