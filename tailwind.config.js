import defaultTheme from 'tailwindcss/defaultTheme';

const sageMintPalette = {
    50: '#F2F9F6',
    100: '#DFF2EA',
    200: '#BFE4D5',
    300: '#94CEB8',
    400: '#68B398',
    500: '#4A947A',
    600: '#397A63',
    700: '#2F6250',
    800: '#274F42',
    900: '#214237',
    950: '#11251E',
};

const terracottaPalette = {
    50: '#FDF5F2',
    100: '#FBE8E1',
    200: '#F6D0C2',
    300: '#EEB09A',
    400: '#E28768',
    500: '#D46542',
    600: '#C04E2C',
    700: '#9E3E22',
    800: '#82341F',
    900: '#6B2D1D',
    950: '#3A150C',
};

const dustyPinkPalette = {
    50: '#FDF4F6',
    100: '#FAE8EC',
    200: '#F5D2DA',
    300: '#ECAFBE',
    400: '#DF8A9F',
    500: '#D4728C',
    600: '#BF5874',
    700: '#A1445E',
    800: '#863A50',
    900: '#723446',
    950: '#401924',
};

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Roboto', 'sans-serif', ...defaultTheme.fontFamily.sans],
                serif: ['Roboto', 'sans-serif'],
            },
            colors: {
                sage: sageMintPalette,
                rose: sageMintPalette,
                dustypink: dustyPinkPalette,
                mint: {
                    50: '#F0FDF7',
                    100: '#DCFCEB',
                    200: '#BBF7D8',
                    300: '#86EFBA',
                    400: '#4ADE96',
                    500: '#2AB876',
                    600: '#1D965E',
                    700: '#19774C',
                    800: '#185E3F',
                    900: '#154E35',
                },
                terracotta: terracottaPalette,
                wine: terracottaPalette,
            },
        },
    },
    plugins: [],
};
