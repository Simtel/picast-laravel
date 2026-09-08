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

const byId = (id) => document.getElementById(id)

const els = {
    nowSeconds: byId('now-seconds'),
    nowUtc: byId('now-utc'),
    nowLocal: byId('now-local'),
    tsInput: byId('ts-input'),
    tsResults: byId('ts-results'),
    tsIso: byId('ts-iso'),
    tsUtc: byId('ts-utc'),
    tsLocal: byId('ts-local'),
    tsRelative: byId('ts-relative'),
    tsError: byId('ts-error'),
    dateInput: byId('date-input'),
    dateTz: byId('date-tz'),
    dateSeconds: byId('date-seconds'),
    dateMs: byId('date-ms'),
    dateError: byId('date-error'),
    exDayLocal: byId('ex-day-local'),
    exDayUtc: byId('ex-day-utc'),
    exMonthLocal: byId('ex-month-local'),
    exMonthUtc: byId('ex-month-utc'),
    exYearLocal: byId('ex-year-local'),
    exYearUtc: byId('ex-year-utc'),
    diffA: byId('diff-a'),
    diffB: byId('diff-b'),
    diffTz: byId('diff-tz'),
    diffResults: byId('diff-results'),
    diffDays: byId('diff-days'),
    diffHours: byId('diff-hours'),
    diffMinutes: byId('diff-minutes'),
    diffSeconds: byId('diff-seconds'),
    diffTotal: byId('diff-total'),
    diffError: byId('diff-error'),
}

function debounce(fn, wait) {
    let timer = null

    return (...args) => {
        window.clearTimeout(timer)
        timer = window.setTimeout(() => fn(...args), wait)
    }
}

function showError(el, message) {
    if (!el) {
        return
    }
    el.textContent = message
    el.classList.remove('d-none')
}

function hideError(el) {
    if (el) {
        el.classList.add('d-none')
    }
}

function setText(el, value) {
    if (el) {
        el.textContent = value
    }
}

function renderTimestampResults(ms) {
    hideError(els.tsError)
    setText(els.tsIso, formatIso(ms))
    setText(els.tsUtc, formatUtcString(ms))
    setText(els.tsLocal, formatLocalString(ms))
    setText(els.tsRelative, formatRelative(ms))
}

function convertTimestamp(value) {
    if (value === '') {
        hideError(els.tsError)
        setText(els.tsIso, '—')
        setText(els.tsUtc, '—')
        setText(els.tsLocal, '—')
        setText(els.tsRelative, '—')
        return null
    }

    const ms = parseTimestampInput(value)
    if (ms === null) {
        setText(els.tsIso, '—')
        setText(els.tsUtc, '—')
        setText(els.tsLocal, '—')
        setText(els.tsRelative, '—')
        showError(els.tsError, 'Не удалось распознать timestamp. Введите целое число — секунды или миллисекунды.')
        return null
    }

    renderTimestampResults(ms)

    return ms
}

function renderDateResult(ms) {
    hideError(els.dateError)
    els.dateSeconds.value = String(Math.floor(ms / 1000))
    els.dateMs.value = String(ms)
}

function convertDate() {
    const value = els.dateInput.value
    const tz = els.dateTz.value
    if (value === '') {
        hideError(els.dateError)
        els.dateSeconds.value = ''
        els.dateMs.value = ''
        return
    }

    const ms = parseHumanDate(value, tz)
    if (ms === null) {
        els.dateSeconds.value = ''
        els.dateMs.value = ''
        showError(els.dateError, 'Не удалось распознать дату. Попробуйте форматы: «2026-09-08 12:00:00», «08.09.2026», «September 8, 2026».')
        return
    }

    renderDateResult(ms)
}

function convertDiff() {
    const a = els.diffA.value
    const b = els.diffB.value
    const tz = els.diffTz.value
    if (a === '' || b === '') {
        hideError(els.diffError)
        els.diffResults.classList.add('d-none')
        return
    }

    const aMs = parseHumanDate(a, tz)
    const bMs = parseHumanDate(b, tz)
    if (aMs === null || bMs === null) {
        els.diffResults.classList.add('d-none')
        showError(els.diffError, 'Не удалось распознать одну из дат. Проверьте формат ввода.')
        return
    }

    hideError(els.diffError)
    const diff = dateDiff(aMs, bMs)
    setText(els.diffDays, String(diff.days))
    setText(els.diffHours, String(diff.hours))
    setText(els.diffMinutes, String(diff.minutes))
    setText(els.diffSeconds, String(diff.seconds))
    setText(els.diffTotal, String(Math.floor(Math.abs(bMs - aMs) / 1000)))
    els.diffResults.classList.remove('d-none')
}

function renderExamples(now) {
    for (const unit of ['day', 'month', 'year']) {
        const localEl = els[`ex${unit.charAt(0).toUpperCase()}${unit.slice(1)}Local`]
        const utcEl = els[`ex${unit.charAt(0).toUpperCase()}${unit.slice(1)}Utc`]
        setText(localEl, String(Math.floor(startOf(unit, 'local', now) / 1000)))
        setText(utcEl, String(Math.floor(startOf(unit, 'utc', now) / 1000)))
    }
}

function copyToClipboard(text, button) {
    const done = () => {
        const original = button.innerHTML
        button.innerHTML = '<i class="fa fa-check"></i>'
        window.setTimeout(() => { button.innerHTML = original }, 1200)
    }
    const fallback = () => {
        const textarea = document.createElement('textarea')
        textarea.value = text
        textarea.style.position = 'fixed'
        textarea.style.opacity = '0'
        document.body.appendChild(textarea)
        textarea.select()
        try {
            document.execCommand('copy')
            done()
        } catch (e) {
            console.warn('[timestamp-page] copy failed', e)
        } finally {
            document.body.removeChild(textarea)
        }
    }

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(done).catch(fallback)
    } else {
        fallback()
    }
}

let lastTsMs = null

function tick(now) {
    setText(els.nowSeconds, String(Math.floor(now / 1000)))
    setText(els.nowUtc, formatUtcString(now))
    setText(els.nowLocal, formatLocalString(now))
    renderExamples(now)
    if (lastTsMs !== null) {
        setText(els.tsRelative, formatRelative(lastTsMs, now))
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (!els.tsInput) {
        return
    }

    const onTsInput = debounce(() => { lastTsMs = convertTimestamp(els.tsInput.value) }, 250)
    els.tsInput.addEventListener('input', onTsInput)

    const onDateInput = debounce(convertDate, 250)
    els.dateInput.addEventListener('input', onDateInput)
    els.dateTz.addEventListener('change', onDateInput)

    const onDiffInput = debounce(convertDiff, 250)
    els.diffA.addEventListener('input', onDiffInput)
    els.diffB.addEventListener('input', onDiffInput)
    els.diffTz.addEventListener('change', onDiffInput)

    for (const button of document.querySelectorAll('[data-copy-target]')) {
        const target = document.getElementById(button.dataset.copyTarget)
        if (target) {
            button.addEventListener('click', () => {
                if (target.value !== '') {
                    copyToClipboard(target.value, button)
                }
            })
        }
    }

    tick(Date.now())
    window.setInterval(() => { tick(Date.now()) }, 1000)
})
