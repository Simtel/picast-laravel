import { describe, expect, it } from 'vitest'
import { diffJson, parseJson, summarize } from './json-diff.js'

describe('parseJson', () => {
    it('parses a valid JSON string', () => {
        expect(parseJson('{"a": 1}')).toEqual({ ok: true, value: { a: 1 } })
        expect(parseJson('[1, 2, 3]')).toEqual({ ok: true, value: [1, 2, 3] })
        expect(parseJson('null')).toEqual({ ok: true, value: null })
    })

    it('rejects empty or non-string input', () => {
        expect(parseJson('').ok).toBe(false)
        expect(parseJson('   ').ok).toBe(false)
        expect(parseJson(null).ok).toBe(false)
    })

    it('returns the error message for invalid JSON', () => {
        const result = parseJson('{not json}')

        expect(result.ok).toBe(false)
        expect(typeof result.error).toBe('string')
        expect(result.error.length).toBeGreaterThan(0)
    })
})

describe('diffJson', () => {
    it('returns no changes for identical values', () => {
        expect(diffJson({ a: 1, b: [1, 2] }, { a: 1, b: [1, 2] })).toEqual([])
    })

    it('detects added and removed top-level keys', () => {
        expect(diffJson({ a: 1 }, { a: 1, b: 2 })).toEqual([
            { path: '$.b', type: 'added', before: undefined, after: 2 },
        ])
        expect(diffJson({ a: 1, b: 2 }, { a: 1 })).toEqual([
            { path: '$.b', type: 'removed', before: 2, after: undefined },
        ])
    })

    it('detects changed primitive values', () => {
        expect(diffJson({ a: 1 }, { a: 2 })).toEqual([
            { path: '$.a', type: 'changed', before: 1, after: 2 },
        ])
    })

    it('recurses into nested objects with deep paths', () => {
        const changes = diffJson(
            { user: { name: 'Ann', age: 30 } },
            { user: { name: 'Ann', age: 31 } },
        )

        expect(changes).toEqual([
            { path: '$.user.age', type: 'changed', before: 30, after: 31 },
        ])
    })

    it('reports a type change at the parent path', () => {
        expect(diffJson({ a: {} }, { a: 5 })).toEqual([
            { path: '$.a', type: 'changed', before: {}, after: 5 },
        ])
    })

    it('compares arrays by index', () => {
        expect(diffJson([1, 2], [1, 2, 3])).toEqual([
            { path: '$[2]', type: 'added', before: undefined, after: 3 },
        ])
        expect(diffJson([1, 2, 3], [1, 2])).toEqual([
            { path: '$[2]', type: 'removed', before: 3, after: undefined },
        ])
        expect(diffJson([1, 2], [1, 9])).toEqual([
            { path: '$[1]', type: 'changed', before: 2, after: 9 },
        ])
    })

    it('quotes object keys that are not simple identifiers', () => {
        expect(diffJson({ 'a.b': 1 }, { 'a.b': 2 })).toEqual([
            { path: '$["a.b"]', type: 'changed', before: 1, after: 2 },
        ])
    })
})

describe('summarize', () => {
    it('counts changes by type', () => {
        const changes = diffJson(
            { a: 1, b: 2, c: 3, d: 4 },
            { a: 1, b: 9, c: 3, e: 5 },
        )

        expect(summarize(changes)).toEqual({ added: 1, removed: 1, changed: 1, total: 3 })
    })

    it('returns zeroed counters for no changes', () => {
        expect(summarize([])).toEqual({ added: 0, removed: 0, changed: 0, total: 0 })
    })
})
