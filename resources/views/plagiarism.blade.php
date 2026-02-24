<!-- <!DOCTYPE html>
<html>

<head>
    <title>Deteksi Plagiarisme Abstrak</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 30px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
        }

        textarea {
            width: 100%;
            height: 180px;
            padding: 10px;
            margin-bottom: 15px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #2c3e50;
            color: #fff;
            border: 0;
            border-radius: 6px;
        }

        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 6px;
            font-weight: bold;
            display: none;
            text-align: center;
        }

        .low {
            background: #d4edda;
            color: #155724;
        }

        .medium {
            background: #fff3cd;
            color: #856404;
        }

        .high {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Deteksi Plagiarisme Abstrak Tugas Akhir</h1>

        <form id="plagiarismForm">
            @csrf
            <label>Abstrak Dokumen 1</label>
            <textarea name="doc1" required></textarea>

            <label>Abstrak Dokumen 2</label>
            <textarea name="doc2" required></textarea>

            <button type="submit">Cek Kemiripan</button>
        </form>

        <div id="resultBox" class="result"></div>
    </div>

    <script>
        document.getElementById("plagiarismForm").addEventListener("submit", async function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            try {
                const res = await fetch("/cek-plagiarisme", {
                    method: "POST",
                    credentials: "same-origin",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "Accept": "application/json"
                    },
                    body: formData
                });

                const data = await res.json();

                if (!res.ok) {
                    throw new Error(data.error || "Request gagal");
                }

                const simNum = Number(data.similarity);
                const sim = simNum.toFixed(2);

                const box = document.getElementById("resultBox");
                box.style.display = "block";

                let kategori = "", kelas = "";
                if (simNum < 20) { kategori = "Rendah (Tidak terindikasi plagiarisme)"; kelas = "low"; }
                else if (simNum < 40) { kategori = "Sedang (Perlu ditinjau)"; kelas = "medium"; }
                else { kategori = "Tinggi (Terindikasi plagiarisme)"; kelas = "high"; }

                box.className = "result " + kelas;
                box.innerHTML = "Kemiripan: " + sim + "%<br>Status: " + kategori;

            } catch (err) {
                console.error(err);
                alert("Gagal cek plagiarisme: " + err.message);
            }
        });
    </script>
</body>

</html> -->