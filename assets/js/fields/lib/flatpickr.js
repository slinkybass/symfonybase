/**
 * Flatpickr helpers for date / datetime / time plugins.
 *
 * Autor: slinkybass
 * Version: 1.0
 *
 * HTML contract. Bool: omitted → default; "false"|"0" → false; else true.
 * enableTime / noCalendar are fixed per plugin (not HTML).
 * Native min/max: date/datetime → minDate/maxDate, then stripped (Flatpickr would re-parse them).
 * Time bounds: data-date-min-time / max-time (native min/max only as fallback, then stripped).
 *
 * Common: alt-*, allow-input, allow-invalid-preload, animate, click-opens,
 * close-on-select, disable-mobile, format, inline, locale, now, position, static, wrap, readonly.
 * Calendar (date/datetime): aria-date-format, default-date, enabled/disabled, month-selector-type,
 * next/prev-arrow, shorthand-current-month, show-months, week-numbers.
 * Clock (datetime/time): auto-fill-default-time, default-hour/minute/seconds, enable-seconds,
 * hour/minute-increment, min-time/max-time, time-24hr.
 * Range (date/datetime plugins; PHP only on *MultipleField): mode, conjunction, range-separator.
 *
 * Not mapped: appendTo, positionElement, formatDate, parseDate, hooks, plugins,
 * errorHandler, ignoredFocusElements, getWeek. parseDate/formatDate use Moment
 * (Flatpickr `H:i` is mapped to Moment `HH:mm`).
 */

import flatpickr from "flatpickr";
import "flatpickr/dist/l10n/index.js";
import "flatpickr/dist/flatpickr.min.css";
import { boolAttr, intAttr, strAttr } from "./plugin.js";

export const ATTR = {
    DATE_PLUGIN: "data-date-field",
    DATETIME_PLUGIN: "data-datetime-field",
    TIME_PLUGIN: "data-time-field",
    ALT_FORMAT: "data-date-alt-format",
    ALT_INPUT: "data-date-alt-input",
    ALT_INPUT_CLASS: "data-date-alt-input-class",
    ALLOW_INPUT: "data-date-allow-input",
    ALLOW_INVALID_PRELOAD: "data-date-allow-invalid-preload",
    ANIMATE: "data-date-animate",
    ARIA_DATE_FORMAT: "data-date-aria-date-format",
    AUTO_FILL_DEFAULT_TIME: "data-date-auto-fill-default-time",
    CLICK_OPENS: "data-date-click-opens",
    CLOSE_ON_SELECT: "data-date-close-on-select",
    CONJUNCTION: "data-date-conjunction",
    DEFAULT_DATE: "data-date-default-date",
    DEFAULT_HOUR: "data-date-default-hour",
    DEFAULT_MINUTE: "data-date-default-minute",
    DEFAULT_SECONDS: "data-date-default-seconds",
    DISABLE_MOBILE: "data-date-disable-mobile",
    DISABLED: "data-date-disabled",
    ENABLE_SECONDS: "data-date-enable-seconds",
    ENABLED: "data-date-enabled",
    FORMAT: "data-date-format",
    HOUR_INCREMENT: "data-date-hour-increment",
    INLINE: "data-date-inline",
    LOCALE: "data-date-locale",
    MAX_TIME: "data-date-max-time",
    MIN_TIME: "data-date-min-time",
    MINUTE_INCREMENT: "data-date-minute-increment",
    MODE: "data-date-mode",
    MONTH_SELECTOR_TYPE: "data-date-month-selector-type",
    NEXT_ARROW: "data-date-next-arrow",
    NOW: "data-date-now",
    POSITION: "data-date-position",
    PREV_ARROW: "data-date-prev-arrow",
    RANGE_SEPARATOR: "data-date-range-separator",
    SHORTHAND_CURRENT_MONTH: "data-date-shorthand-current-month",
    SHOW_MONTHS: "data-date-show-months",
    STATIC: "data-date-static",
    TIME_24HR: "data-date-time-24hr",
    WEEK_NUMBERS: "data-date-week-numbers",
    WRAP: "data-date-wrap",
};

const COMMON_BOOL = [
    [ATTR.ALT_INPUT, "altInput"],
    [ATTR.ALLOW_INVALID_PRELOAD, "allowInvalidPreload"],
    [ATTR.ANIMATE, "animate"],
    [ATTR.CLICK_OPENS, "clickOpens"],
    [ATTR.CLOSE_ON_SELECT, "closeOnSelect"],
    [ATTR.DISABLE_MOBILE, "disableMobile"],
    [ATTR.INLINE, "inline"],
    [ATTR.STATIC, "static"],
    [ATTR.WRAP, "wrap"],
];

const CALENDAR_BOOL = [
    [ATTR.SHORTHAND_CURRENT_MONTH, "shorthandCurrentMonth"],
    [ATTR.WEEK_NUMBERS, "weekNumbers"],
];

const CLOCK_BOOL = [
    [ATTR.AUTO_FILL_DEFAULT_TIME, "autoFillDefaultTime"],
    [ATTR.ENABLE_SECONDS, "enableSeconds"],
    [ATTR.TIME_24HR, "time_24hr"],
];

const COMMON_STR = [
    [ATTR.ALT_FORMAT, "altFormat"],
    [ATTR.ALT_INPUT_CLASS, "altInputClass"],
    [ATTR.FORMAT, "dateFormat"],
    [ATTR.NOW, "now"],
    [ATTR.POSITION, "position"],
];

const CALENDAR_STR = [
    [ATTR.ARIA_DATE_FORMAT, "ariaDateFormat"],
    [ATTR.MONTH_SELECTOR_TYPE, "monthSelectorType"],
    [ATTR.NEXT_ARROW, "nextArrow"],
    [ATTR.PREV_ARROW, "prevArrow"],
];

const RANGE_STR = [
    [ATTR.CONJUNCTION, "conjunction"],
    [ATTR.MODE, "mode"],
];

const CLOCK_INT = [
    [ATTR.DEFAULT_HOUR, "defaultHour", 0],
    [ATTR.DEFAULT_MINUTE, "defaultMinute", 0],
    [ATTR.DEFAULT_SECONDS, "defaultSeconds", 0],
    [ATTR.HOUR_INCREMENT, "hourIncrement", 1],
    [ATTR.MINUTE_INCREMENT, "minuteIncrement", 1],
];

const CALENDAR_INT = [[ATTR.SHOW_MONTHS, "showMonths", 1]];

function mapsForKind(kind) {
    if (kind === "time") {
        return {
            boolMap: [...COMMON_BOOL, ...CLOCK_BOOL],
            strMap: [...COMMON_STR],
            intMap: [...CLOCK_INT],
        };
    }
    if (kind === "date") {
        return {
            boolMap: [...COMMON_BOOL, ...CALENDAR_BOOL],
            strMap: [...COMMON_STR, ...CALENDAR_STR, ...RANGE_STR],
            intMap: [...CALENDAR_INT],
        };
    }
    return {
        boolMap: [...COMMON_BOOL, ...CALENDAR_BOOL, ...CLOCK_BOOL],
        strMap: [...COMMON_STR, ...CALENDAR_STR, ...RANGE_STR],
        intMap: [...CALENDAR_INT, ...CLOCK_INT],
    };
}

/** Flatpickr internals still pass PHP-like tokens (`H:i`); our HTML `data-date-format` is Moment. */
const FLATPICKR_TO_MOMENT = {
    "H:i": "HH:mm",
    "H:i:S": "HH:mm:ss",
    "Y-m-d": "YYYY-MM-DD",
    "Y-m-d H:i": "YYYY-MM-DD HH:mm",
    "Y-m-d H:i:S": "YYYY-MM-DD HH:mm:ss",
    "Y-m-dTH:i": "YYYY-MM-DDTHH:mm",
    "Y-m-dTH:i:S": "YYYY-MM-DDTHH:mm:ss",
};

const PARSE_FALLBACKS = [
    "YYYY-MM-DDTHH:mm:ss",
    "YYYY-MM-DDTHH:mm",
    "YYYY-MM-DD HH:mm:ss",
    "YYYY-MM-DD HH:mm",
    "YYYY-MM-DD",
    "HH:mm:ss",
    "HH:mm",
];

function toMomentFormat(format) {
    if (!format) {
        return format;
    }
    return FLATPICKR_TO_MOMENT[format] ?? format;
}

function parseWithMoment(datestr, format) {
    const formats = [toMomentFormat(format), ...PARSE_FALLBACKS].filter(Boolean);
    for (const token of formats) {
        const parsed = moment(datestr, token, true);
        if (parsed.isValid()) {
            return parsed.toDate();
        }
    }
    const loose = moment(datestr);
    return loose.isValid() ? loose.toDate() : new Date(Number.NaN);
}

function momentHooks() {
    return {
        parseDate: (datestr, format) => parseWithMoment(datestr, format),
        formatDate: (date, format) => moment(date).format(toMomentFormat(format)),
    };
}

function allowInputFromReadonly(el) {
    return el.hasAttribute("readonly") ? el.getAttribute("readonly") === "false" : true;
}

function csvOrString(value) {
    if (value.includes(",")) {
        return value
            .split(",")
            .map((part) => part.trim())
            .filter(Boolean);
    }
    return value;
}

function localIsoDate(date) {
    if (!(date instanceof Date) || Number.isNaN(date.getTime())) {
        return null;
    }
    return new Date(date.getTime() - date.getTimezoneOffset() * 60 * 1000).toISOString().split("T")[0];
}

function asDate(value) {
    if (!value) {
        return null;
    }
    const date = value instanceof Date ? value : parseWithMoment(String(value).trim());
    return Number.isNaN(date.getTime()) ? null : date;
}

function parseCsvDates(csv) {
    return csv
        .split(",")
        .map((token) => asDate(token.trim()))
        .filter(Boolean);
}

function applyMinMaxDate(opts, min, max, { clampMaxToEndOfDay = false } = {}) {
    const maxDate = asDate(max);
    if (maxDate) {
        if (clampMaxToEndOfDay) {
            maxDate.setHours(23, 59, 59, 999);
        }
        opts.maxDate = maxDate;
    }
    const minDate = asDate(min);
    if (minDate) {
        if (clampMaxToEndOfDay) {
            minDate.setHours(0, 0, 0, 0);
        }
        opts.minDate = minDate;
    }
}

function applyTimeBounds(el, opts, minTime, maxTime) {
    if (maxTime) {
        opts.maxTime = maxTime;
        if (!minTime && !el.hasAttribute(ATTR.DEFAULT_HOUR)) {
            applyDefaultClock(opts, maxTime);
        }
    }
    if (minTime) {
        opts.minTime = minTime;
        if (!el.hasAttribute(ATTR.DEFAULT_HOUR)) {
            applyDefaultClock(opts, minTime);
        }
    }
}

function applyCalendarBounds(el, opts, kind, min, max) {
    applyMinMaxDate(opts, min, max, { clampMaxToEndOfDay: kind === "date" });
    applyEnabledDisabledDates(opts, strAttr(el, ATTR.ENABLED), strAttr(el, ATTR.DISABLED));
    if (kind !== "datetime") {
        return;
    }
    if (el.hasAttribute(ATTR.MIN_TIME)) {
        opts.minTime = strAttr(el, ATTR.MIN_TIME);
    }
    if (el.hasAttribute(ATTR.MAX_TIME)) {
        opts.maxTime = strAttr(el, ATTR.MAX_TIME);
    }
}

function applyEnabledDisabledDates(opts, enabledCsv, disabledCsv) {
    if (enabledCsv) {
        const enabled = parseCsvDates(enabledCsv);
        const isoDates = enabled.map(localIsoDate).filter(Boolean);
        opts.enable = [
            function (date) {
                const iso = localIsoDate(date);
                return iso !== null && isoDates.includes(iso);
            },
        ];
        opts.onOpen = function (selectedDates, dateStr, instance) {
            const sorted = [...enabled].sort((a, b) => a - b);
            const first = sorted[0];
            if (!first) {
                return;
            }
            instance.currentMonth = first.getMonth();
            instance.currentYear = first.getFullYear();
            instance.redraw();
        };
        return;
    }

    if (disabledCsv) {
        const isoDates = parseCsvDates(disabledCsv).map(localIsoDate).filter(Boolean);
        opts.disable = [
            function (date) {
                const iso = localIsoDate(date);
                return iso !== null && isoDates.includes(iso);
            },
        ];
    }
}

function applyDefaultClock(opts, clock) {
    const parts = clock.split(":");
    if (parts.length < 2) {
        return;
    }
    const hour = Number.parseInt(parts[0], 10);
    const minute = Number.parseInt(parts[1], 10);
    if (Number.isFinite(hour)) {
        opts.defaultHour = hour;
    }
    if (Number.isFinite(minute)) {
        opts.defaultMinute = minute;
    }
}

function applyLocale(el, opts, kind) {
    const defaultSep = opts.locale?.rangeSeparator ?? ", ";
    const rangeSep = kind === "time" ? defaultSep : strAttr(el, ATTR.RANGE_SEPARATOR, defaultSep);
    const localeKey = strAttr(el, ATTR.LOCALE);
    if (localeKey && flatpickr.l10ns[localeKey]) {
        opts.locale = { ...flatpickr.l10ns[localeKey], rangeSeparator: rangeSep };
        return;
    }
    opts.locale = {
        ...(typeof opts.locale === "object" && opts.locale ? opts.locale : {}),
        rangeSeparator: rangeSep,
    };
}

/**
 * @param {Element} el
 * @param {{ kind: "date"|"datetime"|"time" } & Record<string, unknown>} defaults
 */
export function pickerOptsFromElement(el, defaults) {
    const { kind, ...rest } = defaults;
    const opts = {
        ...rest,
        ...momentHooks(),
    };

    const { boolMap, strMap, intMap } = mapsForKind(kind);
    for (const [attr, key] of boolMap) {
        if (el.hasAttribute(attr)) {
            opts[key] = boolAttr(el, attr, false);
        }
    }
    for (const [attr, key] of strMap) {
        if (el.hasAttribute(attr)) {
            opts[key] = strAttr(el, attr);
        }
    }
    for (const [attr, key, min] of intMap) {
        if (el.hasAttribute(attr)) {
            opts[key] = intAttr(el, attr, opts[key], min);
        }
    }

    if (el.hasAttribute(ATTR.ALLOW_INPUT)) {
        opts.allowInput = boolAttr(el, ATTR.ALLOW_INPUT, true);
    } else {
        opts.allowInput = allowInputFromReadonly(el);
    }

    if (kind !== "time" && el.hasAttribute(ATTR.DEFAULT_DATE)) {
        opts.defaultDate = csvOrString(strAttr(el, ATTR.DEFAULT_DATE));
    }

    applyLocale(el, opts, kind);

    if (opts.inline && !el.hasAttribute(ATTR.ALT_INPUT_CLASS)) {
        opts.altInputClass = "d-none";
    }

    const min = strAttr(el, "min");
    const max = strAttr(el, "max");
    const minTime = strAttr(el, ATTR.MIN_TIME, kind === "time" ? min : null);
    const maxTime = strAttr(el, ATTR.MAX_TIME, kind === "time" ? max : null);
    el.removeAttribute("min");
    el.removeAttribute("max");
    if (kind === "time") {
        applyTimeBounds(el, opts, minTime, maxTime);
    } else {
        applyCalendarBounds(el, opts, kind, min, max);
    }

    return opts;
}

function basePickerDefaults(kind, extra) {
    return {
        kind,
        mode: "single",
        altInput: true,
        disableMobile: true,
        locale: { rangeSeparator: ", " },
        ...extra,
    };
}

export function datePickerDefaults() {
    return basePickerDefaults("date", {
        dateFormat: "YYYY-MM-DD",
        altFormat: moment.localeData().longDateFormat("L"),
        enableTime: false,
        noCalendar: false,
    });
}

export function datetimePickerDefaults(el) {
    const enableSeconds = boolAttr(el, ATTR.ENABLE_SECONDS, false);
    return basePickerDefaults("datetime", {
        dateFormat: "YYYY-MM-DDTHH:mm" + (enableSeconds ? ":ss" : ""),
        altFormat:
            moment.localeData().longDateFormat("L") + " " + moment.localeData().longDateFormat("LT" + (enableSeconds ? "S" : "")),
        enableTime: true,
        noCalendar: false,
        time_24hr: true,
        enableSeconds,
        minuteIncrement: 1,
        hourIncrement: 1,
    });
}

export function timePickerDefaults(el) {
    const enableSeconds = boolAttr(el, ATTR.ENABLE_SECONDS, false);
    return basePickerDefaults("time", {
        dateFormat: enableSeconds ? "HH:mm:ss" : "HH:mm",
        altFormat: moment.localeData().longDateFormat(enableSeconds ? "LTS" : "LT"),
        enableTime: true,
        noCalendar: true,
        time_24hr: true,
        enableSeconds,
        minuteIncrement: 1,
        hourIncrement: 1,
    });
}

export function localizeFlatpickr() {
    const loc = moment.locale();
    const locale = flatpickr.l10ns[loc];
    if (locale) {
        flatpickr.localize(locale);
    }
}

export function createPicker(el, opts) {
    return flatpickr(el, opts);
}
