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
			foreach (array_intersect_key(['endpoint' => '11', 'param' => '22', 'desc' => '22'], $this->params) as $ʟ_v => $ʟ_l) {
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
        <p class="lead">A simple wrapper for Visual Crossing Weather API built with Nette Framework.</p>

        <div class="card mt-4">
            <div class="card-header">
                <h2 class="h4 mb-0">API Endpoints</h2>
            </div>
            <div class="card-body">
';
		foreach ($iterator = $ʟ_it = new Latte\Essential\CachingIterator($endpoints, $ʟ_it ?? null) as $endpoint) /* line 11 */ {
			echo '                    <div class="mb-4">
                        <h3 class="h5">';
			echo LR\Filters::escapeHtmlText($endpoint['name']) /* line 13 */;
			echo '</h3>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-primary me-2">';
			echo LR\Filters::escapeHtmlText($endpoint['method']) /* line 15 */;
			echo '</span>
                            <code>';
			echo LR\Filters::escapeHtmlText($baseUrl) /* line 16 */;
			echo LR\Filters::escapeHtmlText($endpoint['url']) /* line 16 */;
			echo '</code>
                        </div>
                        <p>';
			echo LR\Filters::escapeHtmlText($endpoint['description']) /* line 18 */;
			echo '</p>

                        <h4 class="h6">Parameters</h4>
                        <ul>
';
			foreach ($endpoint['parameters'] as $param => $desc) /* line 22 */ {
				echo '                                <li><strong>';
				echo LR\Filters::escapeHtmlText($param) /* line 23 */;
				echo '</strong> - ';
				echo LR\Filters::escapeHtmlText($desc) /* line 23 */;
				echo '</li>
';

			}

			echo '                        </ul>

                        <h4 class="h6">Example</h4>
                        <div class="d-flex align-items-center">
                            <code>';
			echo LR\Filters::escapeHtmlText($baseUrl) /* line 29 */;
			echo LR\Filters::escapeHtmlText($endpoint['example']) /* line 29 */;
			echo '</code>
                            <a href="';
			echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($endpoint['example'])) /* line 30 */;
			echo '" class="btn btn-sm btn-outline-primary ms-3" target="_blank">Try it</a>
                        </div>
                    </div>
';
			if (!$iterator->isLast()) /* line 33 */ {
				echo '                    <hr>';
			}
			echo "\n";

		}
		$iterator = $ʟ_it = $ʟ_it->getParent();

		echo '            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h2 class="h4 mb-0">Response Format</h2>
            </div>
            <div class="card-body">
                <p>All API responses follow this standard format:</p>
                <pre><code>{
    "status": "success",
    "message": "Success",
    "data": { ... }
}</code></pre>
                <p>In case of errors:</p>
                <pre><code>{
    "status": "error",
    "message": "Error description"
}</code></pre>
            </div>
        </div>
    </div>


';
	}
}
