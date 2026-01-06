tailwind.config = {
    theme: {
        extend: {
            colors: {
                // Override standard colors to neutral grays for professional look
                blue: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b', // Text/Icons usually
                    600: '#475569',
                    700: '#334155',
                    800: '#1e293b', // Primary Action standard
                    900: '#0f172a', // Darkest header
                },
                indigo: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#1e293b',
                    900: '#0f172a',
                },
                purple: {
                    50: '#fafafa',
                    100: '#f4f4f5',
                    200: '#e4e4e7',
                    300: '#d4d4d8',
                    400: '#a1a1aa',
                    500: '#71717a',
                    600: '#52525b',
                    700: '#3f3f46',
                    800: '#27272a',
                    900: '#18181b',
                },
                // Mute success/danger colors to be serious
                green: {
                    50: '#f0fdf4', // Very light for backgrounds
                    100: '#dcfce7',
                    500: '#15803d', // Darker standard green
                    600: '#166534',
                },
                red: {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    500: '#b91c1c', // Dark red
                    600: '#991b1b',
                },
                yellow: {
                    50: '#fefce8',
                    100: '#fef9c3',
                    500: '#a16207', // Dark gold
                    600: '#854d0e',
                }
            },
            borderRadius: {
                'none': '0',
                'sm': '0',
                'DEFAULT': '0',
                'md': '0',
                'lg': '0',
                'xl': '0',
                '2xl': '0',
                '3xl': '0',
                'full': '9999px', // Keep full for circles (profile pics, status dots)
            },
            boxShadow: {
                'sm': '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
                'DEFAULT': '0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1)',
                'md': 'none', // Remove larger shadows for flat look
                'lg': 'none',
                'xl': 'none',
                '2xl': 'none',
            }
        }
    }
}
