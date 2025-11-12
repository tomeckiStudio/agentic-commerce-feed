<div align="center">
  <h1>Agentic Commerce Feed for WooCommerce</h1>
  Export your products to a feed compatible with the Agentic Commerce Protocol by OpenAI. Show your products in ChatGPT's Instant Checkout.
  <br><br>
  <a href="https://x.com/intent/tweet?text=Check%20out%20Agentic%20Commerce%20Feed%20for%20WooCommerce%20%E2%80%94%20export%20products%20to%20the%20Agentic%20Commerce%20Protocol%20by%20OpenAI%20and%20enable%20Instant%20Checkout%20in%20ChatGPT.%20%F0%9F%9A%80&url=https%3A%2F%2Fgithub.com%2FtomeckiStudio%2Fagentic-commerce-feed&hashtags=WooCommerce%2CWordPress%2Cecommerce%2COpenAI%2CChatGPT%2CAgenticCommerceProtocole">
    <img src="https://tomecki.studio/custom-icons/share-icon-x.png" width="92" height="20">
  </a>
  <a href="https://www.linkedin.com/shareArticle?url=https%3A%2F%2Fgithub.com%2FtomeckiStudio%2Fagentic-commerce-feed%2F">
    <img src="https://tomecki.studio/custom-icons/share-icon-linkedin.png" width="137" height="20">
  </a>
</div>
<a id="readme-top"></a>
<br>

> [!CAUTION]
> WARNING!!! The plugin is currently in the testing phase. It is awaiting validation from OpenAI!

> [!IMPORTANT]
> As soon as OpenAI clarifies the open points and there’s interest, I’ll dive into full development. **Can’t wait for v2!**

<details>
<summary><kbd>Table of contents</kbd></summary>

- [Features](#features)
- [Installation](#installation)
- [Limitations](#limitations)
- [Documentation](#documentation)
- [Support](#support)
- [Roadmap](#roadmap)
- [Contributing](#contributing)
- [License](#license)
<br><br>
</details>

## :sparkles: Features
- Export WooCommerce products to an XML file in a format compatible with the Agentic Commerce Protocol by OpenAI. Showcase your products in AI agents using ACP, e.g. ChatGPT's Instant Checkout.
- Schedule feed generation using one of three methods: Woo Action Scheduler, WP-Cron or Server-Cron
- Map product attributes to ACP attributes: You can use WooCommerce values (e.g. for title or description), use product attributes or product metadata (or ACF fields). This makes it easy to use the data you already have in your shop. **And if that's not enough, you can change any attribute using WordPress filters.**
- The plugin reports every build, making it easy to see which products are causing problems.
- Options for developers: WordPress filters and actions

<br>
Available languages:

<div>
    <img src="https://tomecki.studio/custom-icons/flag-united_kingdom.svg" width="30" height="30">
    <img src="https://tomecki.studio/custom-icons/flag-poland.svg" width="30" height="30">
</div>

<div align="right">
  <a href="#readme-top">[Go to top]</a>
</div>

## :computer: Installation
Standard plugin installation on WordPress:
1. Download the plugin .zip file, link: [https://github.com/tomeckiStudio/agentic-commerce-feed/releases/tag/v1.0.0](https://github.com/tomeckiStudio/agentic-commerce-feed/releases/tag/v1.0.0)
2. Go to Plugins -> Add Plugin
3. Click on the "Upload plugin" button
4. Choose the downloaded file and click on the "Install Now" button
5. Activate the plugin

<div align="right">
  <a href="#readme-top">[Go to top]</a>
</div>

## :worried: Limitations
- You cannot export Variants. Currently, only simple products are supported - awaiting clarification from OpenAI
- You can export Geo Price and Geo Availability, but these values are not validated - awaiting clarification from OpenAI
- Problem with postcodes when exporting shipping data - planned improvement in January 26'
- Review data - awaiting clarification from OpenAI

<div align="right">
  <a href="#readme-top">[Go to top]</a>
</div>

## :book: Documentation
Dev docs: https://github.com/tomeckiStudio/agentic-commerce-feed/wiki

<div align="right">
  <a href="#readme-top">[Go to top]</a>
</div>

## :sos: Support
> \[!IMPORTANT]
>
> **I do not offer any guaranteed support.** You use the plugin at your own risk.<br>
> Nevertheless, if you encounter a bug, please raise an issue here. If the bug is caused by a plugin, I will try to fix it in the next version.



<div align="right">
  <a href="#readme-top">[Go to top]</a>
</div>


## :compass: Roadmap

<img src="https://tomecki.studio/projects/plugins/acpfeed/github-roadmap.png" width="1000" height="600" style="width: 100%;">

> \[!NOTE]
>
> These are just plans, so nothing's set in stone.<br>
> I'm developing the plugin in my spare time, so the dates are only approximate.<br>
> It all depends on whether there’s interest in the plugin.
>


01.2026
- **WordPress Plugin Directory!!!** - adding the plugin to the WordPress Plugin Directory to make it even more accessible to everyone!
- **Code optimisation & performance** — load only on cron/admin; avoid loading on every frontend request.  
- **`enable_search` vs. catalog visibility** — honor `product_catalog_visibility` (avoid searchable “private” products).  
- **Lower logging overhead** — reduce I/O and memory footprint, batching where possible. 
- **Shipping export** - fix export of shipping data.

03.2026
- **WooCommerce-respect logging** — normalize to WC logger levels (info/error) and contexts.
- **Optional reports toggle** — allow disabling reports entirely. 
- **Build summary email** — send after build; option: failures only vs. everything.
- **Log size alert** — warn when logs exceed a safe size; add quick “clean” action. 
- **Over-length handling** — if an ACP attribute exceeds limits, trim to fit or skip the product (configurable for each attribute).

05.2026
- **Build summary email** — send after build; option: failures only vs. everything. 
- **ID Duplicate protection** — detect repeated `id` / `mpn` / `gtin` across products.
- **Live field validation** — validate key ACP attributes during product edit (e.g., `title`).
- **Feed versioning & build date** — version token for cache; expose build timestamp.

07.2026
- **Product editor tab** — ACP overrides UI in “Edit product” (friendly filters for non-developers).
- **Exclude rules** — skip by products / categories / tags / status.
- **WPML & multicurrency** — compatibility for language/currency variants
- **Custom product link** — override product URL (e.g., link to a dedicated landing page).

\> 09.2026
- **Attribute fallback chains** — check `pa_material` → `material` → meta `acp_material`, etc.
- **Conditional search/checkout** — e.g., enable only when stock > 0 (policy builder).
- **Other post types** — allow generating feeds for selected custom post types.
- **Incremental builds** — skip unchanged products to speed full builds (where safe).
- **On-change refresh** — rebuild feed on product edits/stock/price updates; choose full vs. per-product.
- **Feed format choice** — JSON or XML. **(storage path selectable)** 

<div align="right">
  <a href="#readme-top">[Go to top]</a>
</div>


## :handshake: Contributing

<img src="https://tomecki.studio/projects/plugins/acpfeed/github-contributing.png" width="1000" height="600" style="width: 100%;">

Please ⭐️ this repository if this project helped you!

- Report issues and bugs on GitHub
- Send your ideas for new features

<a href="https://www.buymeacoffee.com/tomeckiStudio" target="_blank">
  <img src="https://tomecki.studio/custom-icons/icon-buymeacoffee-button.png" alt="Buy Me A Coffee" width="217" height="60">
</a>
<a href="https://www.paypal.com/donate/?hosted_button_id=GESZWLKJ4M7AS" target="_blank">
  <img src="https://tomecki.studio/custom-icons/icon-paypal-donate.png" alt="Make a donation" width="217" height="60">
</a>
<br><br>

> \[!TIP]
>
> As a supporter, you'll have the amazing opportunity to shape the priorities of the roadmap.<br>
> Just write a comment when choosing your support :) <br>
> This is my way of showing you how much I appreciate your help!
>

<div align="right">
  <a href="#readme-top">[Go to top]</a>
</div>


## :page_facing_up: License
[GNU GPL V3](https://github.com/tomeckiStudio/agentic-commerce-feed/blob/main/LICENSE)

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

ADDITIONAL TERMS per GNU GPL Section 7<br>
The origin of the Program MUST NOT be misrepresented; you MUST NOT claim that you wrote the original Program. Altered source versions MUST be plainly marked as such, and MUST NOT be misrepresented as being the original Program.

IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

<div align="right">
  <a href="#readme-top">[Go to top]</a>
</div>
