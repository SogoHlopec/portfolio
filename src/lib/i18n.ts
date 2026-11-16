const dictionaries = {
    en: () => import('@/dictionaries/en.json').then((m) => m.default),
    ru: () => import('@/dictionaries/ru.json').then((m) => m.default),
};

export type Locale = keyof typeof dictionaries;

export const hasLocale = (lang: string): lang is Locale => lang in dictionaries;

export const getDictionary = (locale: Locale) => dictionaries[locale]();
