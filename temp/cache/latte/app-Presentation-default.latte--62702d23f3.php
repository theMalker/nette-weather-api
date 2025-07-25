<?php

declare(strict_types=1);

use Latte\Runtime as LR;

/** source: C:\xampp\htdocs\nette-weather-api\app\Presentation/default.latte */
final class Template_62702d23f3 extends Latte\Runtime\Template
{
	public const Source = 'C:\\xampp\\htdocs\\nette-weather-api\\app\\Presentation/default.latte';

	public const Blocks = [
		['content' => 'blockContent'],
	];


	public function main(array $ʟ_args): void
	{
		extract($ʟ_args);
		unset($ʟ_args);

		if ($this->global->snippetDriver?->renderSnippets($this->blocks[self::LayerSnippet], $this->params)) {
			return;
		}

		$this->renderBlock('content', get_defined_vars()) /* line 1 */;
	}


	public function prepare(): array
	{
		extract($this->params);

		if (!$this->getReferringTemplate() || $this->getReferenceType() === 'extends') {
			foreach (array_intersect_key(['paramName' => '13, 47', 'paramInfo' => '13', 'value' => '20', 'desc' => '20', 'endpoint' => '36', 'paramDesc' => '47', 'exampleTitle' => '54', 'exampleUrl' => '54', 'header' => '93', 'description' => '93'], $this->params) as $ʟ_v => $ʟ_l) {
				trigger_error("Variable \$$ʟ_v overwritten in foreach on line $ʟ_l");
			}
		}
		return get_defined_vars();
	}


	/** {block content} on line 1 */
	public function blockContent(array $ʟ_args): void
	{
		extract($this->params);
		extract($ʟ_args);
		unset($ʟ_args);

		echo '    <div class="container my-5">
        <h1>';
		echo LR\Filters::escapeHtmlText($title) /* line 3 */;
		echo '</h1>
        <p class="lead">';
		echo LR\Filters::escapeHtmlText($apiInfo['description']) /* line 4 */;
		echo '</p>
        <p>This API is powered by <a href="';
		echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($apiInfo['source_url'])) /* line 5 */;
		echo '" target="_blank">';
		echo LR\Filters::escapeHtmlText($apiInfo['source']) /* line 5 */;
		echo '</a>.</p>

        <!-- API Parameters -->
        <div class="card mt-4">
            <div class="card-header">
                <h2 class="h4 mb-0">API Parameters</h2>
            </div>
            <div class="card-body">
';
		foreach ($iterator = $ʟ_it = new Latte\Essential\CachingIterator($parameters, $ʟ_it ?? null) as $paramName => $paramInfo) /* line 13 */ {
			echo '                    <div class="mb-4">
                        <h3 class="h5">';
			echo LR\Filters::escapeHtmlText($paramName) /* line 15 */;
			echo '</h3>
                        <p>';
			echo LR\Filters::escapeHtmlText($paramInfo['description']) /* line 16 */;
			echo '</p>

                        <h4 class="h6">Possible Values</h4>
                        <ul>
';
			foreach ($paramInfo['values'] as $value => $desc) /* line 20 */ {
				echo '                                <li><code>';
				echo LR\Filters::escapeHtmlText($value) /* line 21 */;
				echo '</code> - ';
				echo LR\Filters::escapeHtmlText($desc) /* line 21 */;
				if ($value == $paramInfo['default']) /* line 21 */ {
					echo ' <span class="badge bg-secondary">Default</span>';
				}
				echo '</li>
';

			}

			echo '                        </ul>
                    </div>
';
			if (!$iterator->isLast()) /* line 25 */ {
				echo '                    <hr>';
			}
			echo "\n";

		}
		$iterator = $ʟ_it = $ʟ_it->getParent();

		echo '            </div>
        </div>

        <!-- API Endpoints -->
        <div class="card mt-4">
            <div class="card-header">
                <h2 class="h4 mb-0">API Endpoints</h2>
            </div>
            <div class="card-body">
';
		foreach ($iterator = $ʟ_it = new Latte\Essential\CachingIterator($endpoints, $ʟ_it ?? null) as $endpoint) /* line 36 */ {
			echo '                    <div class="mb-5">
                        <h3 class="h5">';
			echo LR\Filters::escapeHtmlText($endpoint['name']) /* line 38 */;
			echo '</h3>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-primary me-2">';
			echo LR\Filters::escapeHtmlText($endpoint['method']) /* line 40 */;
			echo '</span>
                            <code>';
			echo LR\Filters::escapeHtmlText($baseUrl) /* line 41 */;
			echo LR\Filters::escapeHtmlText($endpoint['url']) /* line 41 */;
			echo '</code>
                        </div>
                        <p>';
			echo LR\Filters::escapeHtmlText($endpoint['description']) /* line 43 */;
			echo '</p>

                        <h4 class="h6">Parameters</h4>
                        <ul>
';
			foreach ($endpoint['parameters'] as $paramName => $paramDesc) /* line 47 */ {
				echo '                                <li><strong>';
				echo LR\Filters::escapeHtmlText($paramName) /* line 48 */;
				echo '</strong> - ';
				echo LR\Filters::escapeHtmlText($paramDesc) /* line 48 */;
				echo '</li>
';

			}

			echo '                        </ul>

                        <h4 class="h6">Example Requests</h4>
                        <div class="list-group mb-3">
';
			foreach ($endpoint['examples'] as $exampleTitle => $exampleUrl) /* line 54 */ {
				echo '                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>';
				echo LR\Filters::escapeHtmlText($exampleTitle) /* line 58 */;
				echo ':</strong>
                                            <code>';
				echo LR\Filters::escapeHtmlText($baseUrl) /* line 59 */;
				echo LR\Filters::escapeHtmlText($exampleUrl) /* line 59 */;
				echo '</code>
                                        </div>
                                        <a href="';
				echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($exampleUrl)) /* line 61 */;
				echo '" class="btn btn-sm btn-outline-primary ms-3" target="_blank">Try it</a>
                                    </div>
                                </div>
';

			}

			echo '                        </div>


                        <h4 class="h6">Example Response</h4>
                        <pre class="bg-light p-3 rounded"><code>';
			echo LR\Filters::escapeHtmlText(json_encode($endpoint['response_example'], JSON_PRETTY_PRINT)) /* line 69 */;
			echo '</code></pre>
                    </div>
';
			if (!$iterator->isLast()) /* line 71 */ {
				echo '                    <hr>';
			}
			echo "\n";

		}
		$iterator = $ʟ_it = $ʟ_it->getParent();

		echo '            </div>
        </div>

        <!-- Rate Limiting -->
        <div class="card mt-4">
            <div class="card-header">
                <h2 class="h4 mb-0">Rate Limiting</h2>
            </div>
            <div class="card-body">
                <p>This API implements rate limiting to ensure fair usage and protect the service from abuse.</p>

                <h4 class="h6">Limits</h4>
                <ul>
                    <li><strong>Requests:</strong> ';
		echo LR\Filters::escapeHtmlText($rateLimit['limit']) /* line 86 */;
		echo ' per ';
		echo LR\Filters::escapeHtmlText($rateLimit['window']) /* line 86 */;
		echo '</li>
                    <li><strong>Identification:</strong> ';
		echo LR\Filters::escapeHtmlText($rateLimit['identification']) /* line 87 */;
		echo '</li>
                </ul>

                <h4 class="h6">Rate Limit Headers</h4>
                <p>Each response includes headers with information about your rate limit status:</p>
                <ul>
';
		foreach ($rateLimit['headers'] as $header => $description) /* line 93 */ {
			echo '                        <li><code>';
			echo LR\Filters::escapeHtmlText($header) /* line 94 */;
			echo '</code>: ';
			echo LR\Filters::escapeHtmlText($description) /* line 94 */;
			echo '</li>
';

		}

		echo '                </ul>

                <h4 class="h6">Exceeding the Rate Limit</h4>
                <p>If you exceed the rate limit, you will receive a <code>429 Too Many Requests</code> response with a message indicating when you can try again.</p>
                <pre class="bg-light p-3 rounded">
                    <code>
{
    "status": "error",
    "message": "Rate limit exceeded. Try again later."
}</code></pre>
            </div>
        </div>

        <!-- Response Format -->
        <div class="card mt-4">
            <div class="card-header">
                <h2 class="h4 mb-0">Response Format</h2>
            </div>
            <div class="card-body">
                <p>All API responses follow this standard format:</p>
                <pre class="bg-light p-3 rounded"><code>{
    "status": "success",
    "message": "Success",
    "data": { ... }
}</code></pre>
                <p>In case of errors:</p>
                <pre class="bg-light p-3 rounded"><code>{
    "status": "error",
    "message": "Error description"
}</code></pre>
            </div>
        </div>

        <footer class="mt-5 text-center text-muted">
            <p>Weather API Wrapper v';
		echo LR\Filters::escapeHtmlText($apiInfo['version']) /* line 130 */;
		echo ' - ';
		echo LR\Filters::escapeHtmlText(date('Y')) /* line 130 */;
		echo '</p>
        </footer>
    </div>

';
	}
}
