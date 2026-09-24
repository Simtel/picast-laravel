<div class="bg-layer" aria-hidden="true">
    <canvas id="tech-bg" class="tech-canvas"></canvas>
</div>

<script>
(function () {
    var canvas = document.getElementById('tech-bg');
    if (!canvas || !canvas.getContext) return;

    var ctx = canvas.getContext('2d');
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var palette = [[34, 211, 238], [99, 102, 241], [168, 85, 247], [226, 232, 240]];
    var w = 0, h = 0, raf = 0, particles = [];
    var LINK = 15000;

    function resize() {
        var dpr = Math.min(window.devicePixelRatio || 1, 2);
        w = canvas.clientWidth;
        h = canvas.clientHeight;
        canvas.width = w * dpr;
        canvas.height = h * dpr;
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

        var count = Math.min(95, Math.round((w * h) / 24000));
        particles = [];
        for (var i = 0; i < count; i++) {
            particles.push({
                x: Math.random() * w,
                y: Math.random() * h,
                vx: (Math.random() - 0.5) * 0.35,
                vy: (Math.random() - 0.5) * 0.35,
                r: Math.random() * 1.8 + 0.6,
                c: palette[(Math.random() * palette.length) | 0]
            });
        }
    }

    function frame() {
        ctx.clearRect(0, 0, w, h);

        var i, j, p, q, dx, dy, d2, alpha;
        for (i = 0; i < particles.length; i++) {
            p = particles[i];
            p.x += p.vx;
            p.y += p.vy;
            if (p.x < -20) p.x = w + 20;
            if (p.x > w + 20) p.x = -20;
            if (p.y < -20) p.y = h + 20;
            if (p.y > h + 20) p.y = -20;
        }

        // ponytail: O(n^2) link pass, capped at ~95 particles so it stays cheap
        for (i = 0; i < particles.length; i++) {
            p = particles[i];
            for (j = i + 1; j < particles.length; j++) {
                q = particles[j];
                dx = p.x - q.x;
                dy = p.y - q.y;
                d2 = dx * dx + dy * dy;
                if (d2 < LINK) {
                    alpha = (1 - d2 / LINK) * 0.35;
                    ctx.strokeStyle = 'rgba(99,102,241,' + alpha.toFixed(3) + ')';
                    ctx.lineWidth = 1;
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y);
                    ctx.lineTo(q.x, q.y);
                    ctx.stroke();
                }
            }
        }

        for (i = 0; i < particles.length; i++) {
            p = particles[i];
            ctx.fillStyle = 'rgba(' + p.c[0] + ',' + p.c[1] + ',' + p.c[2] + ',0.85)';
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fill();
        }

        raf = reduce ? 0 : window.requestAnimationFrame(frame);
    }

    resize();
    frame();

    var resizeTimer;
    window.addEventListener('resize', function () {
        window.clearTimeout(resizeTimer);
        resizeTimer = window.setTimeout(resize, 200);
    });

    document.addEventListener('visibilitychange', function () {
        window.cancelAnimationFrame(raf);
        if (!document.hidden && !reduce) raf = window.requestAnimationFrame(frame);
    });
})();
</script>
