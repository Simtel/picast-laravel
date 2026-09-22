/**
 * Чистая логика сравнения двух JSON-значений.
 * Не обращается к DOM: модуль рассчитан на запуск в node-окружении (Vitest).
 */

/** Тип значения в терминах JSON: 'null' | 'array' | 'object' | 'string' | 'number' | 'boolean'. */
function typeOf(value) {
    if (value === null) {
        return 'null'
    }
    if (Array.isArray(value)) {
        return 'array'
    }

    return typeof value
}

/** Путь к дочернему ключу: `$.foo` для простых имён, `$["a.b"]` — для остальных. */
function childPath(path, key) {
    if (/^[A-Za-z_$][A-Za-z0-9_$]*$/.test(key)) {
        return `${path}.${key}`
    }

    return `${path}[${JSON.stringify(key)}]`
}

/**
 * Разбирает текст в JSON.
 * Возвращает `{ ok: true, value }` либо `{ ok: false, error }` (пустой ввод или невалидный JSON).
 */
export function parseJson(text) {
    if (typeof text !== 'string' || text.trim() === '') {
        return { ok: false, error: 'Пустой ввод' }
    }

    try {
        return { ok: true, value: JSON.parse(text) }
    } catch (e) {
        console.warn('[json-diff.parseJson] не удалось разобрать JSON', { error: e.message })

        return { ok: false, error: e.message }
    }
}

/**
 * Рекурсивно сравнивает два JSON-значения и возвращает список изменений.
 * Запись: `{ path, type: 'added' | 'removed' | 'changed', before, after }`;
 * у `added` отсутствует `before`, у `removed` — `after`.
 * Массивы сравниваются по индексам, объекты — по объединению ключей.
 */
export function diffJson(a, b, path = '$') {
    console.debug('[json-diff.diffJson] start', { path, typeA: typeOf(a), typeB: typeOf(b) })

    const changes = diffValue(a, b, path)
    const summary = summarize(changes)

    console.debug('[json-diff.diffJson] done', summary)

    return changes
}

function diffValue(a, b, path) {
    const typeA = typeOf(a)
    const typeB = typeOf(b)

    if (typeA !== typeB) {
        return [{ path, type: 'changed', before: a, after: b }]
    }

    if (typeA === 'object') {
        const changes = []

        for (const key of new Set([...Object.keys(a), ...Object.keys(b)])) {
            const child = childPath(path, key)
            if (!Object.hasOwn(b, key)) {
                changes.push({ path: child, type: 'removed', before: a[key], after: undefined })
            } else if (!Object.hasOwn(a, key)) {
                changes.push({ path: child, type: 'added', before: undefined, after: b[key] })
            } else {
                changes.push(...diffValue(a[key], b[key], child))
            }
        }

        return changes
    }

    if (typeA === 'array') {
        const changes = []

        for (let i = 0; i < Math.max(a.length, b.length); i++) {
            const child = `${path}[${i}]`
            if (i >= b.length) {
                changes.push({ path: child, type: 'removed', before: a[i], after: undefined })
            } else if (i >= a.length) {
                changes.push({ path: child, type: 'added', before: undefined, after: b[i] })
            } else {
                changes.push(...diffValue(a[i], b[i], child))
            }
        }

        return changes
    }

    if (!Object.is(a, b)) {
        return [{ path, type: 'changed', before: a, after: b }]
    }

    return []
}

/** Счётчики изменений по типам: `{ added, removed, changed, total }`. */
export function summarize(changes) {
    return changes.reduce(
        (acc, change) => {
            acc[change.type]++
            acc.total++

            return acc
        },
        { added: 0, removed: 0, changed: 0, total: 0 },
    )
}
