<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Status - Coffee Ben10</title>
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='12' fill='%230f766e'/%3E%3Ctext x='32' y='39' font-family='Arial,sans-serif' font-size='22' font-weight='700' text-anchor='middle' fill='white'%3ECB%3C/text%3E%3C/svg%3E">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700;800&family=Nunito:wght@700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --brand: #0f766e;
            --accent: #d97706;
            --ink: #0f172a;
            --muted: #64748b;
            --line: rgba(229, 218, 206, 0.4);
            --surface: #ffffff;
            --soft: #faf8f5;
        }

        body {
            background: radial-gradient(circle at top left, #faf8f5, #f1ede4) !important;
            font-family: 'Quicksand', 'Nunito', sans-serif !important;
            color: var(--ink);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        .pickup-header {
            background-color: #ffffff;
            border-bottom: 3px solid var(--line);
            padding: 1.5rem 2.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            z-index: 10;
        }

        .logo-mark {
            width: 50px;
            height: 50px;
            border-radius: 1.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            background: linear-gradient(135deg, var(--brand), var(--accent));
            font-size: 1.5rem;
            box-shadow: 0 8px 20px rgba(15, 118, 110, .2);
        }

        .logo-text {
            font-size: 2.2rem;
            font-weight: 900;
            color: var(--ink);
            letter-spacing: -0.01em;
        }

        .pickup-container {
            flex-grow: 1;
            padding: 2.5rem;
            display: flex;
            gap: 2.5rem;
            height: calc(100vh - 120px);
        }

        .pickup-board {
            flex: 1;
            border-radius: 2.5rem;
            background-color: #ffffff;
            border: 3px solid var(--line);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .board-header {
            padding: 2rem;
            font-weight: 800;
            font-size: 2rem;
            text-align: center;
            border-bottom: 3px solid var(--line);
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .board-preparing {
            border-top: 8px solid #3b82f6;
        }
        .board-preparing .board-header {
            background-color: #eff6ff;
            color: #1d4ed8;
            border-bottom-color: #bfdbfe;
        }

        .board-ready {
            border-top: 8px solid #10b981;
        }
        .board-ready .board-header {
            background-color: #ecfdf5;
            color: #047857;
            border-bottom-color: #bbf7d0;
        }

        .numbers-grid {
            flex-grow: 1;
            padding: 3rem;
            overflow-y: auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 1.5rem;
            align-content: start;
        }

        .number-cell {
            font-size: 2.8rem;
            font-weight: 900;
            border-radius: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            aspect-ratio: 1.2;
            box-shadow: 0 6px 15px rgba(0,0,0,0.01);
            transition: all 0.3s;
        }

        .preparing-cell {
            background-color: #f8fafc;
            border: 2px solid #e2e8f0;
            color: #475569;
            animation: fadeInNumber 0.5s ease;
        }

        .ready-cell {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #ffffff;
            border: none;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
            animation: readyPulse 2s infinite alternate, fadeInNumber 0.5s ease;
        }

        @keyframes fadeInNumber {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        @keyframes readyPulse {
            0% { transform: scale(1); box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3); }
            50% { transform: scale(1.05); box-shadow: 0 15px 35px rgba(16, 185, 129, 0.45); }
            100% { transform: scale(1); box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3); }
        }

        .empty-label {
            grid-column: 1 / -1;
            text-align: center;
            font-size: 1.8rem;
            color: var(--muted);
            margin-top: 8rem;
            font-weight: 600;
            opacity: 0.6;
        }

        .pickup-footer {
            background-color: var(--brand);
            color: #ffffff;
            text-align: center;
            font-weight: 700;
            font-size: 1.25rem;
            padding: 1.25rem;
            margin-top: auto;
            letter-spacing: 0.05em;
            box-shadow: 0 -4px 15px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

    <header class="pickup-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <span class="logo-mark"><i class="bi bi-cup-hot-fill"></i></span>
            <span class="logo-text">Coffee Ben10</span>
        </div>
        <div class="fs-4 fw-bold text-muted" id="clock-display">
            <!-- Dynamic local time -->
        </div>
    </header>

    <div class="pickup-container">
        <!-- Preparing Column -->
        <div class="pickup-board board-preparing">
            <div class="board-header">
                <div>Preparing</div>
                <div class="small fw-semibold fs-5 opacity-75">កំពុងឆុង</div>
            </div>
            <div class="numbers-grid" id="grid-preparing">
                <div class="empty-label">No orders preparing</div>
            </div>
        </div>

        <!-- Ready Column -->
        <div class="pickup-board board-ready">
            <div class="board-header">
                <div>Ready to Pick Up</div>
                <div class="small fw-semibold fs-5 opacity-75">សូមអញ្ជើញមកទទួល</div>
            </div>
            <div class="numbers-grid" id="grid-ready">
                <div class="empty-label">No orders ready yet</div>
            </div>
        </div>
    </div>

    <!-- Alert sound chime -->
    <audio id="alert-ding" src="https://assets.mixkit.co/active_storage/sfx/2013/2013-84.wav" preload="auto"></audio>

    <footer class="pickup-footer">
        <i class="bi bi-bell-fill me-2"></i> PLEASE PRESENT YOUR RECEIPT WHEN COLLECTING YOUR ORDER / សូមបង្ហាញវិក្កយបត្រនៅពេលមកទទួលភេសជ្ជៈ
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ding = document.getElementById('alert-ding');
            let knownReadyNumbers = new Set();
            let isFirstLoad = true;

            function playDing() {
                try {
                    ding.play();
                } catch (e) {
                    console.log('Audio ding blocked by browser configs');
                }
            }

            function updateClock() {
                const now = new Date();
                let hrs = now.getHours();
                const mins = String(now.getMinutes()).padStart(2, '0');
                const ampm = hrs >= 12 ? 'PM' : 'AM';
                hrs = hrs % 12;
                hrs = hrs ? hrs : 12; // 12 instead of 0
                document.getElementById('clock-display').innerText = `${hrs}:${mins} ${ampm}`;
            }
            setInterval(updateClock, 1000);
            updateClock();

            function updatePickup() {
                fetch('/pickup/state')
                    .then(r => r.json())
                    .then(state => {
                        renderPickup(state);
                    })
                    .catch(e => console.error('Pickup sync error:', e));
            }

            function renderPickup(state) {
                const gridPrep = document.getElementById('grid-preparing');
                const gridReady = document.getElementById('grid-ready');

                let prepHtml = '';
                let readyHtml = '';
                let playSound = false;

                // 1. Render preparing
                if (state.preparing.length === 0) {
                    prepHtml = '<div class="empty-label">No orders preparing</div>';
                } else {
                    state.preparing.forEach(num => {
                        prepHtml += `<div class="number-cell preparing-cell">#${num}</div>`;
                    });
                }
                gridPrep.innerHTML = prepHtml;

                // 2. Render ready
                if (state.ready.length === 0) {
                    readyHtml = '<div class="empty-label">No orders ready yet</div>';
                } else {
                    state.ready.forEach(num => {
                        readyHtml += `<div class="number-cell ready-cell">#${num}</div>`;
                        
                        // Check if order number just entered ready state
                        if (!knownReadyNumbers.has(num)) {
                            knownReadyNumbers.add(num);
                            if (!isFirstLoad) {
                                playSound = true;
                            }
                        }
                    });
                }
                gridReady.innerHTML = readyHtml;

                // Cleanup stale ready numbers
                knownReadyNumbers.forEach(num => {
                    if (!state.ready.includes(num)) {
                        knownReadyNumbers.delete(num);
                    }
                });

                if (playSound) {
                    playDing();
                }

                isFirstLoad = false;
            }

            updatePickup();
            setInterval(updatePickup, 2000);
        });
    </script>
</body>
</html>
