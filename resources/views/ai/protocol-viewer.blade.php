<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Protokół — {{ $conversation->title }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #2c3e50; font-family: 'Segoe UI', Arial, sans-serif; }

        .toolbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 52px;
            background: #1a252f;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 16px;
            z-index: 100;
            box-shadow: 0 2px 8px rgba(0,0,0,.4);
        }
        .toolbar-title {
            color: #c8d8e4;
            font-size: 13px;
            font-weight: 600;
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .toolbar-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            border: none;
            white-space: nowrap;
        }
        .btn-download { background: #e74c3c; color: #fff; }
        .btn-download:hover { background: #c0392b; }
        .btn-close { background: #4a6375; color: #fff; }
        .btn-close:hover { background: #355468; }
        .page-nav {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .page-nav button {
            background: #34495e;
            color: #ecf0f1;
            border: none;
            border-radius: 6px;
            padding: 5px 10px;
            font-size: 13px;
            cursor: pointer;
        }
        .page-nav button:hover { background: #4a6375; }
        .page-nav button:disabled { opacity: .4; cursor: default; }
        #page-info { color: #a0b4c3; font-size: 12px; min-width: 70px; text-align: center; }

        .viewer-wrap {
            margin-top: 52px;
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            min-height: calc(100vh - 52px);
        }

        .pdf-page {
            background: #fff;
            box-shadow: 0 4px 24px rgba(0,0,0,.5);
            display: block;
        }

        #loading {
            color: #a0b4c3;
            font-size: 15px;
            margin-top: 60px;
            text-align: center;
        }
        #loading .spinner {
            display: inline-block;
            width: 32px; height: 32px;
            border: 3px solid #4a6375;
            border-top-color: #0e89d8;
            border-radius: 50%;
            animation: spin .8s linear infinite;
            margin-bottom: 12px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        #error-msg {
            display: none;
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
            border-radius: 10px;
            padding: 16px 24px;
            margin-top: 40px;
            font-size: 14px;
            max-width: 480px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="toolbar">
    <div class="toolbar-title">📋 {{ $conversation->title }}</div>
    <div class="page-nav">
        <button id="btn-prev" onclick="changePage(-1)" disabled>◀</button>
        <span id="page-info">…</span>
        <button id="btn-next" onclick="changePage(1)" disabled>▶</button>
    </div>
    <a href="{{ $pdfUrl }}" download class="toolbar-btn btn-download">📥 Pobierz PDF</a>
    <button onclick="window.close()" class="toolbar-btn btn-close">✕ Zamknij</button>
</div>

<div class="viewer-wrap" id="viewer">
    <div id="loading">
        <div class="spinner"></div><br>
        Ładowanie dokumentu…
    </div>
    <div id="error-msg">
        Nie udało się załadować dokumentu PDF.<br>
        <a href="{{ $pdfUrl }}" download style="color:#991b1b;font-weight:700;">Pobierz plik PDF</a>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js" crossorigin="anonymous"></script>
<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc =
        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    const PDF_URL = @json($pdfUrl);
    const viewer  = document.getElementById('viewer');
    const loading = document.getElementById('loading');
    const errorMsg = document.getElementById('error-msg');

    let pdfDoc     = null;
    let totalPages = 0;
    let currentPage = 1;
    const canvases = [];
    const SCALE = Math.min(window.devicePixelRatio || 1, 2) * 1.4;

    function updateNav() {
        document.getElementById('page-info').textContent = `${currentPage} / ${totalPages}`;
        document.getElementById('btn-prev').disabled = currentPage <= 1;
        document.getElementById('btn-next').disabled = currentPage >= totalPages;
    }

    function changePage(delta) {
        const target = currentPage + delta;
        if (target < 1 || target > totalPages) return;
        currentPage = target;
        canvases[currentPage - 1]?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        updateNav();
    }

    async function renderPage(pdf, num) {
        const page = await pdf.getPage(num);
        const viewport = page.getViewport({ scale: SCALE });

        const canvas = document.createElement('canvas');
        canvas.className = 'pdf-page';
        canvas.width  = viewport.width;
        canvas.height = viewport.height;
        canvas.style.width  = Math.floor(viewport.width  / SCALE) + 'px';
        canvas.style.height = Math.floor(viewport.height / SCALE) + 'px';
        viewer.appendChild(canvas);
        canvases.push(canvas);

        await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;
    }

    async function loadPdf() {
        try {
            const loadingTask = pdfjsLib.getDocument({
                url: PDF_URL,
                withCredentials: true,
            });
            pdfDoc = await loadingTask.promise;
            totalPages = pdfDoc.numPages;
            loading.style.display = 'none';

            for (let i = 1; i <= totalPages; i++) {
                await renderPage(pdfDoc, i);
            }

            updateNav();

            // Update page-info on scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        currentPage = canvases.indexOf(e.target) + 1;
                        updateNav();
                    }
                });
            }, { threshold: 0.5 });
            canvases.forEach(c => observer.observe(c));

        } catch (err) {
            console.error('PDF load error:', err);
            loading.style.display = 'none';
            errorMsg.style.display = 'block';
        }
    }

    loadPdf();
</script>
</body>
</html>
