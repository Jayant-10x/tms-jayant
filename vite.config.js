import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // css
                'resources/scss/app.scss',
                'resources/scss/icons.scss',
                'node_modules/swiper/swiper-bundle.min.css',
                'node_modules/nouislider/dist/nouislider.min.css',
                'node_modules/choices.js/public/assets/styles/choices.min.css',
                'node_modules/swiper/swiper-bundle.min.css',
                // 'node_modules/fullcalendar/main.min.css',
                // 'node_modules/gridjs/dist/theme/mermaid.min.css',
                'node_modules/flatpickr/dist/flatpickr.min.css',
                'node_modules/quill/dist/quill.snow.css',
                'node_modules/quill/dist/quill.bubble.css',
                'node_modules/swiper/swiper-bundle.min.css',
                'resources/css/project.css',
                'resources/css/tasks.css',
                'resources/css/team-members.css',

                // js
                'resources/js/app.js',
                'resources/js/pages/project-task-form.js',
                'resources/js/pages/tasks.js',
                'resources/js/pages/projects.js',
                'resources/js/pages/employee.js',
                'resources/js/pages/my-profile.js',
                'resources/js/config.js',
                'resources/js/layout.js',
                'resources/js/pages/dashboard-analytics.js',
                'resources/js/pages/dashboard.js',
                'resources/js/pages/dashboard-customer.js',
                'resources/js/pages/property-grid.js',
                'resources/js/components/form-fileupload.js',
                'resources/js/pages/agent-grid.js',
                'resources/js/pages/agent-detail.js',
                'resources/js/pages/customer-detail.js',
                'resources/js/pages/app-chat.js',
                'resources/js/pages/app-blog-create.js',
                'resources/js/pages/app-blog-create.js',
                'resources/js/pages/app-calendar.js',
                'resources/js/pages/coming-soon.js',
                'resources/js/pages/widgets.js',
                'resources/js/pages/calendar.js',
                // 'resources/js/components/maps-google.js',
                /*'resources/js/components/maps-vector.js',
                'resources/js/components/maps-canada.js',
                'resources/js/components/maps-iraq.js',
                'resources/js/components/maps-russia.js',
                'resources/js/components/maps-spain.js',
                'resources/js/components/table-gridjs.js',
                'resources/js/components/form-clipboard.js',
                'resources/js/components/form-flatepicker.js',
                'resources/js/components/form-wizard.js',*/
                'resources/js/components/form-fileupload.js',
                /*'resources/js/components/form-quilljs.js',
                'resources/js/components/form-slider.js',
                'resources/js/components/apexchart-area.js',
                'resources/js/components/apexchart-bar.js',
                'resources/js/components/apexchart-bubble.js',
                'resources/js/components/apexchart-candlestick.js',
                'resources/js/components/apexchart-column.js',
                'resources/js/components/apexchart-heatmap.js',
                'resources/js/components/apexchart-line.js',
                'resources/js/components/apexchart-mixed.js',
                'resources/js/components/apexchart-timeline.js',
                'resources/js/components/apexchart-boxplot.js',
                'resources/js/components/apexchart-treemap.js',
                'resources/js/components/apexchart-pie.js',
                'resources/js/components/apexchart-radar.js',
                'resources/js/components/apexchart-radialbar.js',
                'resources/js/components/apexchart-scatter.js',
                'resources/js/components/apexchart-polar-area.js',
                // 'resources/js/components/extended-rating.js',
                'resources/js/components/extended-sweetalert.js'*/
            ],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        /*host: '0.0.0.0', // Allows network devices to access the Vite dev server
        hmr: {
            host: 'http://localhost:8000', // Change to your actual machine LAN IP
        },*/
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
