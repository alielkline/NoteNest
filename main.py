from collections import defaultdict
import sys
input = sys.stdin.read

MOD = 132120577

def solve():
    data = input().split()
    idx = 0
    T = int(data[idx])
    idx += 1
    results = []
    
    for _ in range(T):
        N = int(data[idx])
        idx += 1

        freq = defaultdict(int)
        values = []

        for _ in range(N):
            x = int(data[idx])
            f = int(data[idx + 1])
            idx += 2
            freq[x] += f
            values.append(x)

        # Deduplicate and sort
        values = sorted(set(values))

        diff_count = defaultdict(int)
        
        # Count all unordered pairs grouped by |Xi - Xj|
        for i in range(len(values)):
            xi, fi = values[i], freq[values[i]]
            # Same value pairs
            diff_count[0] += fi * (fi - 1) // 2
            for j in range(i+1, len(values)):
                xj, fj = values[j], freq[values[j]]
                d = xj - xi
                diff_count[d] += fi * fj
        
        # Build prefix sum for queries
        max_diff = max(diff_count) if diff_count else 0
        prefix_sum = [0] * (max_diff + 2)
        for d in range(max_diff + 1):
            prefix_sum[d + 1] = (prefix_sum[d] + diff_count.get(d, 0)) % MOD

        Q = int(data[idx])
        idx += 1

        for _ in range(Q):
            l = int(data[idx])
            r = int(data[idx + 1])
            idx += 2
            r = min(r, max_diff)
            l = min(l, max_diff + 1)
            if l > r:
                results.append("0")
            else:
                res = (prefix_sum[r + 1] - prefix_sum[l]) % MOD
                results.append(str(res))
    
    print("\n".join(results))

