# Simple Document Management System (SDMS)
A WordPress plugin to manage documents with multilingual support, custom categories, and secure file handling.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#features)
  - [Adding a New Document](#adding-a-new-document)
  - [Accessing Documents](#accessing-documents)
- [Templates](#templates)
- [Security Considerations](#security-considerations)
- [Contributing](#contributing)
- [License](#license)
- [Credits](#credits)


## Features <a name="features"></a>
- **Custom Post Type:** Adds a "Document" post type with hierarchical categories.
- **Multilingual Support:** Upload and manage files in multiple languages per document.
- **Custom Permalinks:** Generates URLs like /docs/category/subcategory/document/.
- **Secure File Downloads:** Serves files through controlled URLs to prevent direct access.
- **Customizable Templates:** Select different front-end templates for document display.
- **Custom File Type Icons:** Upload custom icons for various file types.
- **Admin Settings Page:** Configure languages, templates, and icons through an intuitive interface.
- **WordPress Standards:** Built following WordPress coding best practices.


## Installation <a name="installation"></a>
1. **Download** the plugin files and upload them to your WordPress installation under the wp-content/plugins/smds directory.
2. **Activate** the plugin through the WordPress admin dashboard:
  - Navigate to **Plugins**.
  - Click **Activate** next to "Simple Document Management System (SDMS)".
3. Flush Rewrite Rules:
  - Go to **Settings > Permalinks**.
  - Click **Save Changes** to refresh permalinks and enable custom URLs.


## Configuration <a name="configuration"></a>
1. Navigate to **Settings > SDMS Settings** in the WordPress admin dashboard.
2. **Add Languages**:
  - Select languages from the dropdown menu.
  - Click **Add** to include them.
  - Upload custom flags for each language if desired.
3. **Customize File Type Icons**:
  - Upload custom icons for different file types (PDF, Word, Excel, etc.).
4. **Select a Template**:
  - Choose a front-end template from the available options.
5. **Save Changes** to apply your settings.


## Usage <a name="usage"></a>

### Adding a New Document <a name="adding-a-new-document"></a>
1. Go to Documents > Add New.
2. Enter Title and Content for your document.
3. Assign Categories specific to the Document post type.
4. Upload Files for Each Language:
  - In the Language Files metabox, upload files for the languages you've added.
  - You can view or remove files before saving.
5. Select File Type Image:
  - Choose an image representing the file type, displayed on the front end.
6. Publish the document.


### Accessing Documents <a name="accessing-documents"></a>
- Front-End URL Structure:
  - Access documents via URLs like : https://yourdomain.com/docs/category/document/

- Downloading Files:
  - Default Language : https://yourdomain.com/docs/category/document/download/
  - Specific Language : https://yourdomain.com/docs/category/document/download/{language_code}


## Templates <a name="templates"></a>
- Template Selection:
  - Choose templates from the settings page to change how documents are displayed.
- Customization:
  - Templates are located in the templates directory.
  - Use the same CSS file for consistent styling across templates.
- Creating Custom Templates:
  - You can create additional templates by adding PHP files in the templates directory.
  - Ensure your templates follow WordPress theme standards.


## Security Considerations <a name="security-considerations"></a>
- File Validation:
  - Only allows specific file types to prevent malicious uploads.
- File Size Limit:
  - Enforces maximum upload size to maintain performance.
- User Permissions:
  - Only administrators can change plugin settings.
  - Editors can add or modify documents and custom fields.
- Protected File URLs:
  - Direct access to file URLs is prevented; files are served through controlled endpoints.
- Data Sanitization:
  - All user inputs are sanitized and validated.
- Nonce Verification:
  - Uses nonces for security checks on form submissions.


## Contributing <a name="contributing"></a>
Contributions are welcome! Please submit issues and pull requests on the GitHub repository.


## License <a name="license"></a>
This plugin is licensed under the GNU General Public License v2.0 or later.


## Credits <a name="credits"></a>
Developed by Dorian Renon
