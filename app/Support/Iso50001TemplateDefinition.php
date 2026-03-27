<?php

namespace App\Support;

class Iso50001TemplateDefinition
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function defaultSteps(): array
    {
        return [
            [
                'key' => 'context',
                'title' => 'Krok 1: Kontekst organizacji',
                'description' => 'Okresl podstawowe informacje o organizacji i zakresie systemu zarzadzania energia.',
                'fields' => [
                    [
                        'name' => 'organization_scope',
                        'label' => 'Zakres systemu zarzadzania energia',
                        'type' => 'textarea',
                        'required' => true,
                        'help' => 'Opisz lokalizacje, procesy i obszary, ktore obejmuje audyt ISO 50001.',
                    ],
                    [
                        'name' => 'energy_policy_exists',
                        'label' => 'Czy firma posiada formalna polityke energetyczna?',
                        'type' => 'select',
                        'required' => true,
                        'options' => ['tak', 'nie'],
                        'help' => 'Wybierz tak, jesli polityka jest zatwierdzona i zakomunikowana pracownikom.',
                    ],
                    [
                        'name' => 'energy_policy_details',
                        'label' => 'Jesli tak, opisz najwazniejsze elementy polityki',
                        'type' => 'textarea',
                        'required' => false,
                        'depends_on' => 'energy_policy_exists',
                        'depends_value' => 'tak',
                    ],
                ],
            ],
            [
                'key' => 'baseline',
                'title' => 'Krok 2: Przeglad energetyczny',
                'description' => 'Zidentyfikuj istotne obszary zuzycia energii i poziom bazowy.',
                'fields' => [
                    [
                        'name' => 'significant_energy_uses',
                        'label' => 'Najwazniejsze obszary zuzycia energii (SEU)',
                        'type' => 'textarea',
                        'required' => true,
                        'help' => 'Przyklad: linie produkcyjne, systemy HVAC, sprezarki, oswietlenie.',
                    ],
                    [
                        'name' => 'energy_baseline_year',
                        'label' => 'Rok bazowy do porownan',
                        'type' => 'number',
                        'required' => true,
                    ],
                    [
                        'name' => 'metering_complete',
                        'label' => 'Czy system opomiarowania pokrywa wszystkie kluczowe obszary?',
                        'type' => 'select',
                        'required' => true,
                        'options' => ['tak', 'czesciowo', 'nie'],
                    ],
                ],
            ],
            [
                'key' => 'goals',
                'title' => 'Krok 3: Cele i plan dzialan',
                'description' => 'Zdefiniuj cele energetyczne, dzialania i odpowiedzialnosci.',
                'fields' => [
                    [
                        'name' => 'objectives_defined',
                        'label' => 'Czy cele energetyczne sa formalnie zdefiniowane?',
                        'type' => 'select',
                        'required' => true,
                        'options' => ['tak', 'nie'],
                    ],
                    [
                        'name' => 'objectives_list',
                        'label' => 'Lista celow energetycznych na najblizsze 12 miesiecy',
                        'type' => 'textarea',
                        'required' => true,
                    ],
                    [
                        'name' => 'action_plan_owner',
                        'label' => 'Osoba odpowiedzialna za realizacje planu',
                        'type' => 'text',
                        'required' => true,
                    ],
                ],
            ],
            [
                'key' => 'monitoring',
                'title' => 'Krok 4: Monitoring i wskazniki',
                'description' => 'Ustal jak monitorowane sa wskazniki EnPI i zgodnosc z celami.',
                'fields' => [
                    [
                        'name' => 'kpi_defined',
                        'label' => 'Czy zdefiniowano wskazniki EnPI?',
                        'type' => 'select',
                        'required' => true,
                        'options' => ['tak', 'nie'],
                    ],
                    [
                        'name' => 'kpi_examples',
                        'label' => 'Przykladowe wskazniki EnPI',
                        'type' => 'textarea',
                        'required' => false,
                        'depends_on' => 'kpi_defined',
                        'depends_value' => 'tak',
                    ],
                    [
                        'name' => 'data_update_frequency',
                        'label' => 'Czestotliwosc aktualizacji danych energetycznych',
                        'type' => 'text',
                        'required' => true,
                    ],
                ],
            ],
            [
                'key' => 'competence',
                'title' => 'Krok 5: Kompetencje i audyt wewnetrzny',
                'description' => 'Sprawdz przygotowanie organizacji w obszarze kompetencji i audytow wewnetrznych.',
                'fields' => [
                    [
                        'name' => 'training_plan_exists',
                        'label' => 'Czy istnieje plan szkolen energetycznych?',
                        'type' => 'select',
                        'required' => true,
                        'options' => ['tak', 'nie'],
                    ],
                    [
                        'name' => 'internal_audit_done',
                        'label' => 'Czy przeprowadzono audyt wewnetrzny EnMS w ostatnich 12 miesiacach?',
                        'type' => 'select',
                        'required' => true,
                        'options' => ['tak', 'nie'],
                    ],
                    [
                        'name' => 'internal_audit_date',
                        'label' => 'Data ostatniego audytu wewnetrznego',
                        'type' => 'date',
                        'required' => false,
                        'depends_on' => 'internal_audit_done',
                        'depends_value' => 'tak',
                    ],
                ],
            ],
            [
                'key' => 'summary',
                'title' => 'Krok 6: Podsumowanie i deklaracja',
                'description' => 'Podsumuj gotowosc organizacji oraz kluczowe obszary wymagajace poprawy.',
                'fields' => [
                    [
                        'name' => 'main_gaps',
                        'label' => 'Najwazniejsze luki do zamkniecia przed certyfikacja',
                        'type' => 'textarea',
                        'required' => true,
                    ],
                    [
                        'name' => 'target_certification_date',
                        'label' => 'Planowana data gotowosci do certyfikacji',
                        'type' => 'date',
                        'required' => true,
                    ],
                    [
                        'name' => 'client_declaration',
                        'label' => 'Deklaracja klienta (osoba odpowiedzialna)',
                        'type' => 'text',
                        'required' => true,
                        'help' => 'Wpisz imie i nazwisko osoby zatwierdzajacej kompletnosc formularza.',
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<mixed> $steps
     * @return array<int, array<string, mixed>>
     */
    public static function normalizeSteps(array $steps): array
    {
        $normalized = [];

        foreach ($steps as $step) {
            if (! is_array($step)) {
                continue;
            }

            $fields = [];
            foreach ((array) ($step['fields'] ?? []) as $field) {
                if (! is_array($field)) {
                    continue;
                }

                $name = trim((string) ($field['name'] ?? ''));
                $label = trim((string) ($field['label'] ?? ''));
                if ($name === '' || $label === '') {
                    continue;
                }

                $type = trim((string) ($field['type'] ?? 'text'));
                if (! in_array($type, ['text', 'textarea', 'select', 'number', 'date'], true)) {
                    $type = 'text';
                }

                $options = [];
                if ($type === 'select') {
                    $options = array_values(array_filter(array_map(
                        fn ($item) => trim((string) $item),
                        (array) ($field['options'] ?? [])
                    )));
                }

                $fields[] = [
                    'name' => $name,
                    'label' => $label,
                    'type' => $type,
                    'required' => (bool) ($field['required'] ?? false),
                    'help' => trim((string) ($field['help'] ?? '')),
                    'depends_on' => trim((string) ($field['depends_on'] ?? '')),
                    'depends_value' => trim((string) ($field['depends_value'] ?? '')),
                    'options' => $options,
                ];
            }

            if ($fields === []) {
                continue;
            }

            $key = trim((string) ($step['key'] ?? ''));
            if ($key === '') {
                $key = 'step_'.(count($normalized) + 1);
            }

            $normalized[] = [
                'key' => $key,
                'title' => trim((string) ($step['title'] ?? 'Krok '.(count($normalized) + 1))),
                'description' => trim((string) ($step['description'] ?? '')),
                'fields' => $fields,
            ];
        }

        return $normalized !== [] ? $normalized : self::defaultSteps();
    }

    /**
     * @param array<int, array<string, mixed>> $steps
     */
    public static function maxTasks(array $steps): int
    {
        return collect($steps)->sum(fn ($step) => count((array) ($step['fields'] ?? [])));
    }

    /**
     * @param array<string, mixed> $answers
     * @param array<int, array<string, mixed>> $steps
     */
    public static function filledTasks(array $answers, array $steps): int
    {
        $filled = 0;

        foreach ($steps as $step) {
            $stepKey = (string) ($step['key'] ?? '');
            $stepAnswers = (array) ($answers[$stepKey] ?? []);

            foreach ((array) ($step['fields'] ?? []) as $field) {
                $name = (string) ($field['name'] ?? '');
                $value = $stepAnswers[$name] ?? null;

                if (self::hasValue($value)) {
                    $filled++;
                }
            }
        }

        return $filled;
    }

    private static function hasValue(mixed $value): bool
    {
        if ($value === null) {
            return false;
        }

        if (is_string($value)) {
            return trim($value) !== '';
        }

        if (is_array($value)) {
            return $value !== [];
        }

        return true;
    }
}
