import { diffLines, splitLines, summarize } from './text-diff.js'

const byId = (id) => document.getElementById(id)

const els = {
    a: byId('text-diff-a'),
    b: byId('text-diff-b'),
    run: byId('text-diff-run'),
    error: byId('text-diff-error'),
    summary: byId('text-diff-summary'),
    result: byId('text-diff-result'),
}

const COUNTERS = [
    { type: 'added', label: 'Добавлено', className: 'text-success' },
    { type: 'removed', label: 'Удалено', className: 'text-danger' },
    { type: 'unchanged', label: 'Без изменений', className: 'text-muted' },
]

const ROW_CLASSES = { added: 'table-success', removed: 'table-danger' }
const MARKERS = { added: '+', removed: '−', equal: ' ' }

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

function lineCell(number) {
    const cell = document.createElement('td')
    cell.className = 'text-muted text-end'
    cell.style.width = '3.5rem'
    cell.textContent = number ?? ''

    return cell
}

/** Безопасная отрисовка (textContent, без innerHTML): строки — недоверенный пользовательский текст. */
function renderResult(changes) {
    els.result.replaceChildren()

    if (changes.length === 0) {
        const empty = document.createElement('div')
        empty.className = 'text-muted'
        empty.textContent = 'Оба текста пусты'
        els.result.appendChild(empty)

        return
    }

    const table = document.createElement('table')
    table.className = 'table table-sm font-monospace mb-0 align-middle'

    const body = document.createElement('tbody')
    for (const change of changes) {
        const row = document.createElement('tr')
        row.className = ROW_CLASSES[change.type] ?? ''

        row.appendChild(lineCell(change.aLine))
        row.appendChild(lineCell(change.bLine))

        const marker = document.createElement('td')
        marker.className = 'text-center fw-bold'
        marker.style.width = '1.5rem'
        marker.textContent = MARKERS[change.type] ?? ''
        row.appendChild(marker)

        const text = document.createElement('td')
        text.className = 'text-break'
        text.textContent = change.text === '' ? ' ' : change.text
        row.appendChild(text)

        body.appendChild(row)
    }
    table.appendChild(body)

    els.result.appendChild(table)
}

function compare() {
    try {
        const changes = diffLines(splitLines(els.a.value), splitLines(els.b.value))
        const counts = summarize(changes)

        hideError()
        console.debug('[text-diff.page] сравнение выполнено', counts)

        renderSummary(counts)
        renderResult(changes)
    } catch (e) {
        console.warn('[text-diff.page] ошибка сравнения', { error: e.message })
        showError(e.message)
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (!els.a || !els.b || !els.run) {
        return
    }

    console.debug('[text-diff.page] DOM готов')

    els.run.addEventListener('click', compare)
    els.a.addEventListener('input', hideError)
    els.b.addEventListener('input', hideError)
})
