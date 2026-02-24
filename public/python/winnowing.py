import sys
import json
import re

def preprocess(text: str) -> str:
    text = (text or "").lower()
    return re.sub(r"[^a-z0-9]+", "", text)

def kgrams(text: str, k: int):
    return [text[i:i+k] for i in range(len(text) - k + 1)]

def rolling_hash(kgrams_list, base=101, mod=2**32):
    if not kgrams_list:
        return []
    k = len(kgrams_list[0])
    h = 0
    hashes = []

    for ch in kgrams_list[0]:
        h = (h * base + ord(ch)) % mod
    hashes.append(h)

    high_base = pow(base, k - 1, mod)

    for i in range(1, len(kgrams_list)):
        prev = kgrams_list[i-1]
        curr = kgrams_list[i]
        h = (h - ord(prev[0]) * high_base) % mod
        h = (h * base + ord(curr[-1])) % mod
        hashes.append(h)

    return hashes

def winnowing(hashes, w: int):
    fp = set()
    if w <= 0 or len(hashes) < w:
        return fp

    for i in range(len(hashes) - w + 1):
        window = hashes[i:i+w]
        m = min(window)
        # ambil posisi minimum paling kanan (stabil)
        j = (w - 1) - window[::-1].index(m)
        fp.add((m, i + j))
    return fp

def fingerprint(text: str, k: int, w: int):
    t = preprocess(text)
    if len(t) < k:
        return set()
    kg = kgrams(t, k)
    hashes = rolling_hash(kg)
    return winnowing(hashes, w)

def similarity(fp1, fp2):
    s1 = {h for h, _ in fp1}
    s2 = {h for h, _ in fp2}
    if not s1 and not s2:
        return 0.0
    inter = s1 & s2
    union = s1 | s2
    return (len(inter) / len(union)) * 100.0

def main():
    try:
        raw = sys.stdin.read()
        data = json.loads(raw)

        query = data.get("query", "")
        docs = data.get("documents", [])
        k = int(data.get("k", 7))
        w = int(data.get("w", 5))
        top_n = int(data.get("top_n", 10))

        fp_query = fingerprint(query, k, w)

        results = []
        for d in docs:
            doc_id = d.get("id")
            title = d.get("judul", "")
            text = d.get("text", "")

            fp_doc = fingerprint(text, k, w)
            sim = similarity(fp_query, fp_doc)

            results.append({
                "id": doc_id,
                "judul": title,
                "similarity": sim
            })

        results.sort(key=lambda x: x["similarity"], reverse=True)

        best = results[0]["similarity"] if results else 0.0

        out = {
            "best_similarity": best,
            "top": results[:top_n],
            "count": len(results)
        }

        print(json.dumps(out, ensure_ascii=False))
    except Exception as e:
        print(f"ERROR: {e}", file=sys.stderr)
        sys.exit(1)

if __name__ == "__main__":
    main()