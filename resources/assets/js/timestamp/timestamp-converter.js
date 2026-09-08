export const MAX_DATE_MS = 8.64e15

const ISO_PARTS = /^(\d{4})-(\d{2})-(\d{2})(?:[T ](\d{1,2}):(\d{2})(?::(\d{2})(?:\.\d{1,3})?)?)?(?:\s*(Z|[+-]\d{2}:?\d{2}))?$/
const RU_PARTS = /^(\d{1,2})[-./](\d{1,2})[-./](\d{4})(?:[T ](\d{1,2}):(\d{2})(?::(\d{2}))?)?\s*$/
const HAS_EXPLICIT_ZONE = /\b(Z|GMT|UTC)\b|[+-]\d{2}:?\d{2}$/i

/**
 * Разбирает введённый Unix timestamp (секунды или миллисекунды) и возвращает
 * epoch в миллисекундах. Автоопределение: |n| >= 1e12 — миллисекунды,
 * иначе секунды. Возвращает null для пустого/невалидного ввода.
 */
export function parseTimestampInput(value) {
    const str = String(value ?? '').trim()
    if (str === '') {
        return null
    }
    if (!/^-?\d+$/.test(str)) {
        return null
    }

    const n = Number(str)
    if (!Number.isSafeInteger(n)) {
        return null
    }

    const ms = Math.abs(n) >= 1e12 ? n : n * 1000
    if (Math.abs(ms) > MAX_DATE_MS) {
        return null
    }

    return ms
}

function isExact(date, year, month, day, hours, minutes, seconds, utc) {
    const getYear = utc ? date.getUTCFullYear() : date.getFullYear()
    const getMonth = utc ? date.getUTCMonth() : date.getMonth()
    const getDate = utc ? date.getUTCDate() : date.getDate()
    const getHours = utc ? date.getUTCHours() : date.getHours()
    const getMinutes = utc ? date.getUTCMinutes() : date.getMinutes()
    const getSeconds = utc ? date.getUTCSeconds() : date.getSeconds()

    return getYear === year
        && getMonth === month - 1
        && getDate === day
        && getHours === hours
        && getMinutes === minutes
        && getSeconds === seconds
}

function isWithinRange(ms) {
    return Number.isFinite(ms) && Math.abs(ms) <= MAX_DATE_MS
}

/**
 * Переводит числовые компоненты даты/времени в epoch (мс).
 * wallEpochFrame: при явном смещении offsetMin или tz='utc' сравнивает
 * переполнение компонентов в UTC-кадре, иначе — в локальном.
 */
function numericPartsToMs(parts, tz, offsetMin) {
    const [year, month, day, hours, minutes, seconds] = parts

    if (offsetMin !== null) {
        const wallEpoch = Date.UTC(year, month - 1, day, hours, minutes, seconds)
        const wall = new Date(wallEpoch)
        if (!isExact(wall, ...parts, true)) {
            return null
        }
        const epoch = wallEpoch - offsetMin * 60 * 1000

        return isWithinRange(epoch) ? epoch : null
    }

    if (tz === 'utc') {
        const wall = new Date(Date.UTC(year, month - 1, day, hours, minutes, seconds))
        if (!isExact(wall, ...parts, true)) {
            return null
        }

        return wall.getTime()
    }

    const date = new Date(year, month - 1, day, hours, minutes, seconds)
    if (!isExact(date, ...parts, false)) {
        return null
    }

    return date.getTime()
}

/**
 * Разбирает человекочитаемую строку с датой в epoch (мс).
 * Числовые форматы: YYYY-MM-DD[ HH:MM[:SS]], DD.MM.YYYY / DD/MM/YYYY /
 * DD-MM-YYYY [+время], ISO c Z/смещением — по компонентам (без Date.parse,
 * чтобы не зависеть от DST). Текстовые с названием месяца (September 8, 2026)
 * — через Date.parse; при tz='utc' и отсутствии явной зоны компоненты
 * трактуются как UTC-стеновые часы. Возвращает null на нераспознанном вводе.
 */
export function parseHumanDate(input, tz = 'local') {
    const str = String(input ?? '').trim()
    if (str === '') {
        return null
    }

    let m = str.match(ISO_PARTS)
    if (m) {
        const parts = [Number(m[1]), Number(m[2]), Number(m[3]), Number(m[4] ?? 0), Number(m[5] ?? 0), Number(m[6] ?? 0)]

        return numericPartsToMs(parts, tz, parseZoneOffset(m[7]))
    }

    m = str.match(RU_PARTS)
    if (m) {
        const parts = [Number(m[3]), Number(m[2]), Number(m[1]), Number(m[4] ?? 0), Number(m[5] ?? 0), Number(m[6] ?? 0)]

        return numericPartsToMs(parts, tz, null)
    }

    if (HAS_EXPLICIT_ZONE.test(str)) {
        const date = new Date(str)
        return isValidDate(date) ? date.getTime() : null
    }

    const date = new Date(str)
    if (!isValidDate(date)) {
        return null
    }
    if (tz === 'utc') {
        return Date.UTC(
            date.getFullYear(), date.getMonth(), date.getDate(),
            date.getHours(), date.getMinutes(), date.getSeconds()
        )
    }

    return date.getTime()
}

function parseZoneOffset(zone) {
    if (zone === undefined) {
        return null
    }
    if (zone === 'Z') {
        return 0
    }
    const sign = zone.startsWith('-') ? -1 : 1
    const digits = zone.replace(/^[+-]/, '').replace(':', '')
    const hours = Number(digits.slice(0, 2))
    const minutes = Number(digits.slice(2, 4))

    return sign * (hours * 60 + minutes)
}

function isValidDate(date) {
    return !Number.isNaN(date.getTime())
}

/** ISO 8601 (UTC) либо null для невалидного timestamp. */
export function formatIso(ms) {
    const date = toDate(ms)

    return date === null ? null : date.toISOString()
}

/** Строка вида "Mon, 08 Sep 2026 12:00:00 GMT" либо null. */
export function formatUtcString(ms) {
    const date = toDate(ms)

    return date === null ? null : date.toUTCString()
}

/** Локальное время браузера пользователя либо null. */
export function formatLocalString(ms) {
    const date = toDate(ms)

    return date === null ? null : date.toString()
}

function toDate(ms) {
    if (typeof ms !== 'number' || !Number.isFinite(ms) || Math.abs(ms) > MAX_DATE_MS) {
        return null
    }
    const date = new Date(ms)

    return isValidDate(date) ? date : null
}

const SECONDS = 1000
const MINUTES = 60 * SECONDS
const HOURS = 60 * MINUTES
const DAYS = 24 * HOURS

function pluralRu(n, one, few, many) {
    const abs = Math.abs(n)
    const mod10 = abs % 10
    const mod100 = abs % 100
    if (mod10 === 1 && mod100 !== 11) {
        return one
    }
    if (mod10 >= 2 && mod10 <= 4 && (mod100 < 12 || mod100 > 14)) {
        return few
    }

    return many
}

/**
 * Относительное время по-русски ("2 часа назад", "через 5 минут").
 * Значения округляются до старшей единицы (годы > дни > часы > минуты > секунды).
 */
export function formatRelative(ms, nowMs) {
    const now = Number.isFinite(nowMs) ? nowMs : Date.now()
    if (typeof ms !== 'number' || !Number.isFinite(ms) || Math.abs(ms) > MAX_DATE_MS) {
        return null
    }

    const diff = ms - now
    if (diff === 0) {
        return 'только что'
    }

    const totalAbs = Math.abs(diff)
    const future = diff > 0

    const prefix = future ? 'через ' : ''
    const suffix = future ? '' : ' назад'

    const years = Math.floor(totalAbs / DAYS / 365.2425)
    if (years >= 1) {
        return `${prefix}${years} ${pluralRu(years, 'год', 'года', 'лет')}${suffix}`
    }

    const days = Math.floor(totalAbs / DAYS)
    if (days >= 1) {
        return `${prefix}${days} ${pluralRu(days, 'день', 'дня', 'дней')}${suffix}`
    }

    const hours = Math.floor(totalAbs / HOURS)
    if (hours >= 1) {
        return `${prefix}${hours} ${pluralRu(hours, 'час', 'часа', 'часов')}${suffix}`
    }

    const minutes = Math.floor(totalAbs / MINUTES)
    if (minutes >= 1) {
        return `${prefix}${minutes} ${pluralRu(minutes, 'минуту', 'минуты', 'минут')}${suffix}`
    }

    const seconds = Math.floor(totalAbs / SECONDS)
    if (seconds >= 1) {
        return `${prefix}${seconds} сек.${suffix}`
    }

    return 'только что'
}

/**
 * Начало дня/месяца/года для заданного момента в tz ('local'|'utc').
 * Возвращает epoch в мс либо null для невалидного nowMs.
 */
export function startOf(unit, tz, nowMs) {
    if (!Number.isFinite(nowMs) || Math.abs(nowMs) > MAX_DATE_MS) {
        return null
    }
    const date = new Date(nowMs)
    if (!isValidDate(date)) {
        return null
    }

    const y = date.getFullYear()
    const month = date.getMonth()
    const day = date.getDate()

    if (tz === 'utc') {
        const dateUtc = new Date(nowMs)
        const yUtc = dateUtc.getUTCFullYear()
        const mUtc = dateUtc.getUTCMonth()
        const dUtc = dateUtc.getUTCDate()
        if (unit === 'day') {
            return Date.UTC(yUtc, mUtc, dUtc)
        }
        if (unit === 'month') {
            return Date.UTC(yUtc, mUtc, 1)
        }

        return Date.UTC(yUtc, 0, 1)
    }

    if (unit === 'day') {
        return new Date(y, month, day).setHours(0, 0, 0, 0)
    }
    if (unit === 'month') {
        return new Date(y, month, 1).setHours(0, 0, 0, 0)
    }

    return new Date(y, 0, 1).setHours(0, 0, 0, 0)
}

/** Разница между двумя epoch (мс): целые секунды/минуты/часы/дни. */
export function dateDiff(aMs, bMs) {
    if (!Number.isFinite(aMs) || !Number.isFinite(bMs)) {
        return null
    }
    const diff = Math.abs(bMs - aMs)
    const days = Math.floor(diff / DAYS)
    const hours = Math.floor((diff % DAYS) / HOURS)
    const minutes = Math.floor((diff % HOURS) / MINUTES)
    const seconds = Math.floor((diff % MINUTES) / SECONDS)

    return { days, hours, minutes, seconds }
}
