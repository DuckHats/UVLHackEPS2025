/**
 * Theme composable for consistent styling across components
 */
export function useTheme() {
    const colors = {
        primary: '#fbbf24',      // yellow-400
        primaryDark: '#b45309',  // yellow-700
        secondary: '#8fce00',    // lime-green
        background: '#1f2937',   // gray-800
        backgroundLight: '#374151', // gray-700
        text: '#f3f4f6',         // gray-100
        textMuted: '#9ca3af',    // gray-400
        border: '#4b5563',       // gray-600
        borderAccent: 'rgba(251, 191, 36, 0.4)', // yellow with opacity
    };

    const spacing = {
        cardPadding: 'p-6',
        sectionGap: 'gap-10',
        itemGap: 'gap-4',
    };

    const animations = {
        fadeIn: {
            initial: { opacity: 0, y: 50 },
            enter: {
                opacity: 1,
                y: 0,
                transition: { duration: 800, type: 'spring' }
            }
        },
        slideIn: {
            initial: { opacity: 0, x: -50 },
            enter: {
                opacity: 1,
                x: 0,
                transition: { duration: 600, type: 'spring' }
            }
        }
    };

    const cardStyles = {
        base: 'bg-gray-900/30 p-6 rounded-xl border border-yellow-700/20',
        hover: 'hover:bg-gray-900/40 transition-colors',
        shadow: 'shadow-2xl'
    };

    return {
        colors,
        spacing,
        animations,
        cardStyles
    };
}
