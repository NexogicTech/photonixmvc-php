# JSON 输出

```
use App\\baseController;

class Api extends baseController
{
    public function data(Request $request): string
    {
        return $this->json(['message' => 'ok', 'data' => ['q' => $request->get()]]);
    }
}
```

- `json(array $data, int $code=200, bool $isShowCode=true)` 返回统一 JSON 结构，中文不转义。