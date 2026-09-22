import { diffJson, parseJson, summarize } from './json-diff.js'

const byId = (id) => document.getElementById(id)

const els = {
    a: byId('json-diff-a'),
    b: byId('json-diff-b'),
    run: byId('json-diff-run'),
    error: byId('json-diff-error'),
    summary: byId('json-diff-summary'),
    result: byId('json-diff-result'),
}

const TYPE_LABELS = { added: 'добавлено', removed: 'удалено', changed: 'изменено' }
const TYPE_CLASSES = { added: 'text-success', removed: 'text-danger', changed: 'text-warning' }
const COUNTERS = [
    { type: 'added', label: 'Добавлено', className: 'text-success' },
    { type: 'removed', label: 'Удалено', className: 'text-danger' },
    { type: 'changed', label: 'Изменено', className: 'text-warning' },
]

/** Значение из diff → строка для вывода; отсутствующее значение показываем прочерком. */
function displayValue(value) {
    return value === undefined ? '—' : JSON.stringify(value)
}

function showError(message) {
    els.error.textContent = message
    els.error.classList.remove('d-none')
}

function hideError() {
    els.error.classList.add('d-none')
}

function renderSummary(counts) {
    els.summary.replaceChildren()

    for (const { type, label, className } of COUNTERS) {
        const badge = document.createElement('span')
        badge.className = `badge bg-light border ${className}`
        badge.textContent = `${label}: ${counts[type]}`
        els.summary.appendChild(badge)
    }
}

function valueLine(prefix, value, className) {
    const line = document.createElement('div')
    line.className = `small text-break ${className}`
    line.textContent = `${prefix} ${displayValue(value)}`

    return line
}

/** Безопасная отрисовка (textContent, без innerHTML): значения — недоверенный пользовательский JSON. */
function renderResult(changes) {
    els.result.replaceChildren()

    if (changes.length === 0) {
        const empty = document.createElement('div')
        empty.className = 'text-muted'
        empty.textContent = 'Изменений нет'
        els.result.appendChild(empty)

        return
    }

    const list = document.createElement('ul')
    list.className = 'list-unstyled mb-0 d-flex flex-column gap-2'

    for (const change of changes) {
        const item = document.createElement('li')
        item.className = 'border rounded p-2'

        const header = document.createElement('div')
        header.className = 'd-flex align-items-center gap-2 mb-1'

        const path = document.createElement('code')
        path.textContent = change.path
        header.appendChild(path)

        const type = document.createElement('span')
        type.className = 'badge bg-secondary'
        type.textContent = TYPE_LABELS[change.type] ?? change.type
        header.appendChild(type)
        item.appendChild(header)

        if (change.type !== 'added') {
            item.appendChild(valueLine('−', change.before, 'text-danger'))
        }
        if (change.type !== 'removed') {
            item.appendChild(valueLine('+', change.after, 'text-success'))
        }

        list.appendChild(item)
    }

    els.result.appendChild(list)
}

function compare() {
    const parsedA = parseJson(els.a.value)
    if (!parsedA.ok) {
        console.warn('[json-diff.page] невалидный JSON в поле A', { error: parsedA.error })
        showError(`JSON в поле «A»: ${parsedA.error}`)

        return
    }

    const parsedB = parseJson(els.b.value)
    if (!parsedB.ok) {
        console.warn('[json-diff.page] невалидный JSON в поле B', { error: parsedB.error })
        showError(`JSON в поле «B»: ${parsedB.error}`)

        return
    }

    hideError()

    const changes = diffJson(parsedA.value, parsedB.value)
    const counts = summarize(changes)
    console.debug('[json-diff.page] сравнение выполнено', counts)

    renderSummary(counts)
    renderResult(changes)
}

document.addEventListener('DOMContentLoaded', () => {
    if (!els.a || !els.b || !els.run) {
        return
    }

    console.debug('[json-diff.page] DOM готов')

    els.run.addEventListener('click', compare)
    els.a.addEventListener('input', hideError)
    els.b.addEventListener('input', hideError)
})
