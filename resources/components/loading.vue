<template>
    <Spin fix>
        <div class="loading-spinner">
            <svg viewBox="25 25 50 50" class="circular">
                <circle cx="50" cy="50" r="20" fill="none" class="path"></circle>
            </svg>
        </div>
    </Spin>
</template>

<script>
    export default {
        name: 'loading'
    };
</script>

<style lang="less">
    .loading-spinner {
    .circular {
        height: 36px;
        width: 36px;
        animation: loading-rotate 2s linear infinite;
    }
    .path {
        animation: loading-dash 1.5s ease-in-out infinite;
        stroke-dasharray: 90,150;
        stroke-dashoffset: 0;
        stroke-width: 2;
        stroke: #2d8cf0;
        stroke-linecap: round;
    }
    }
    @keyframes loading-rotate {
        100% {
            transform: rotate(1turn);
        }
    }
    @keyframes loading-dash {
        0% {
            stroke-dasharray: 1,200;
            stroke-dashoffset: 0;
        }
        50% {
            stroke-dasharray: 90,150;
            stroke-dashoffset: -40px;
        }
        100% {
            stroke-dasharray: 90,150;
            stroke-dashoffset: -120px;
        }
    }

</style>
