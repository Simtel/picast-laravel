/**
 * Чистая логика построчного сравнения двух текстов.
 * Не обращается к DOM: модуль рассчитан на запуск в node-окружении (Vitest).
 */

// ponytail: O(n*m) LCS-таблица; для гигантских текстов сравнение отключено (MAX_CELLS)
const MAX_CELLS = 4_000_000

/** Разбивает текст на строки, нормализуя переводы строк CRLF/CR → LF. */
export function splitLines(text) {
    if (typeof text !== 'string' || text === '') {
        return []
    }

    return text.replace(/\r\n?/g, '\n').split('\n')
}

/**
 * Сравнивает два массива строк и возвращает операции в порядке следования.
 * Запись: `{ type: 'equal' | 'added' | 'removed', aLine, bLine, text }`;
 * у `added` нет `aLine`, у `removed` — `bLine` (номера строк с 1).
 */
export function diffLines(a, b) {
    const n = a.length
    const m = b.length

    if ((n + 1) * (m + 1) > MAX_CELLS) {
        throw new Error('Слишком большой объём текста для сравнения')
    }

    const width = m + 1
    const lcs = new Int32Array((n + 1) * width)

    for (let i = n - 1; i >= 0; i--) {
        for (let j = m - 1; j >= 0; j--) {
            lcs[i * width + j] = a[i] === b[j]
                ? lcs[(i + 1) * width + (j + 1)] + 1
                : Math.max(lcs[(i + 1) * width + j], lcs[i * width + (j + 1)])
        }
    }

    const result = []
    let i = 0
    let j = 0

    while (i < n && j < m) {
        if (a[i] === b[j]) {
            result.push({ type: 'equal', aLine: i + 1, bLine: j + 1, text: a[i] })
            i++
            j++
        } else if (lcs[(i + 1) * width + j] >= lcs[i * width + (j + 1)]) {
            result.push({ type: 'removed', aLine: i + 1, text: a[i] })
            i++
        } else {
            result.push({ type: 'added', bLine: j + 1, text: b[j] })
            j++
        }
    }

    while (i < n) {
        result.push({ type: 'removed', aLine: i + 1, text: a[i] })
        i++
    }

    while (j < m) {
        result.push({ type: 'added', bLine: j + 1, text: b[j] })
        j++
    }

    return result
}

/** Счётчики по типам строк: `{ added, removed, unchanged, total }`. */
export function summarize(changes) {
    return changes.reduce(
        (acc, change) => {
            if (change.type === 'added') {
                acc.added++
            } else if (change.type === 'removed') {
                acc.removed++
            } else {
                acc.unchanged++
            }
            acc.total = acc.added + acc.removed

            return acc
        },
        { added: 0, removed: 0, unchanged: 0, total: 0 },
    )
}
