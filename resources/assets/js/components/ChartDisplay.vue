<script>
    import { Bar, Pie, PolarArea, Doughnut } from 'vue-chartjs';

    export default {
        props : {
        //     chartType: '',
            label: 'Label'
        },
        extends: Bar,
        mounted () {
            // Overwriting base render method with actual data.
            this.renderChart({
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                datasets: [
                    {
                        // label: 'A simple label',
                        label: 'Label',
                        backgroundColor: '#f87979',
                        data: [40, 20, 12, 39, 10, 40, 39, 80, 40, 20, 12, 11]
                    }
                ]
            })
        }
    }

</script>