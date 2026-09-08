import { defineConfig } from 'vitest/config'

export default defineConfig({
    test: {
        environment: 'node',
        include: ['resources/assets/js/**/*.test.js'],
        env: { TZ: 'UTC' },
    },
})
