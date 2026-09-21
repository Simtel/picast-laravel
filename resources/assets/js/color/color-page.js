import { describeColor, parseColor, rgbToHsl } from './color-converter.js'

const byId = (id) => document.getElementById(id)

const els = {
    picker: byId('color-picker'),
    preview: byId('color-preview'),
    previewLabel: byId('color-preview-label'),
    hex: byId('color-hex'),
    rgb: byId('color-rgb'),
    hsl: byId('color-hsl'),
    name: byId('color-name'),
    error: byId('color-error'),
}

const FIELDS = [els.hex, els.rgb, els.hsl, els.name]

const UNSUPPORTED_MESSAGE = 'Не удалось распознать цвет. Поддерживаются: #rgb, #rrggbb, rgb(r, g, b), hsl(h, s%, l%) '
    + 'и именованные цвета CSS (148 названий). Альфа-канал (rgba, #rrggbbaa, transparent) не поддерживается.'

/** Единое состояние страницы: только он источник истины, все поля производны от него. */
let state = null

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

function setPreviewContrast(color) {
    const { l } = rgbToHsl(color.r, color.g, color.b)
    const dark = l >= 60

    els.preview.classList.toggle('text-dark', dark)
    els.preview.classList.toggle('text-white', !dark)
}

/** Перерисовывает все четыре поля, пикер и превью из состояния и снимает ошибки. */
function applyColor(color) {
    const { hex, rgb, hsl, name } = describeColor(color)

    els.hex.value = hex
    els.rgb.value = rgb
    els.hsl.value = hsl
    els.name.value = name ?? ''
    els.picker.value = hex
    els.preview.style.backgroundColor = hex
    els.previewLabel.textContent = hex
    setPreviewContrast(color)

    for (const field of FIELDS) {
        field.classList.remove('is-invalid')
    }
    hideError(els.error)

    console.debug('[color-page] состояние обновлено', { r: color.r, g: color.g, b: color.b })
}

function onFieldChange(field) {
    const value = field.value
    console.debug('[color-page] поле изменено', { field: field.id, value })

    if (value.trim() === '') {
        applyColor(state)
        return
    }

    const color = parseColor(value)
    if (color === null) {
        field.classList.add('is-invalid')
        showError(els.error, UNSUPPORTED_MESSAGE)
        console.warn('[color-page] не удалось распознать цвет', { field: field.id, value })
        return
    }

    state = color
    applyColor(state)
}

function onPickerInput() {
    const value = els.picker.value
    console.debug('[color-page] пикер изменён', { value })

    const color = parseColor(value)
    if (color === null) {
        showError(els.error, UNSUPPORTED_MESSAGE)
        console.warn('[color-page] не удалось распознать цвет', { field: 'color-picker', value })
        return
    }

    state = color
    applyColor(state)
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
            console.warn('[color-page] копирование не удалось', e)
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

document.addEventListener('DOMContentLoaded', () => {
    if (!els.picker) {
        return
    }

    const initial = parseColor(els.picker.value)
    if (initial === null) {
        showError(els.error, UNSUPPORTED_MESSAGE)
        console.warn('[color-page] не удалось распознать стартовый цвет', { value: els.picker.value })
        return
    }

    state = initial
    console.debug('[color-page] инициализация', { color: initial })
    applyColor(state)

    els.picker.addEventListener('input', onPickerInput)

    for (const field of FIELDS) {
        field.addEventListener('change', () => { onFieldChange(field) })
        field.addEventListener('input', () => { field.classList.remove('is-invalid') })
    }

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
})
