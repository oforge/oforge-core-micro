# Ideen und Vorschläge



- AssetBundles
    - Kompilierung von custom Bundles (anstatt nur Backend/Frontend), das Laden ist bereits eingebaut.
	- JS: Angabe von script/components Dependencys
	- Versionsangabe mit Versionskonflicküberprüfung (zB jQuery 2 & jQuery 3)
- Themes & Theme-Kompilierung
    - Nutzung von Variablen verbessern
    - Child-Theme Kompilierung (Parent/Base nicht komplett integrieren, Auswahlmöglichkeit von ala @import in SCSS) \
        js import ala scss: anstatt Dateiname im aktuellen Verzeichnis relative Dateipfade
- Bootvorgang
    - Load Methode erst nach Initialiserung aller Module & Plugins
    - Module in eigenen Ordner wie Plugins, wie Plugins behandeln \
      Extraction von Funktionen/JS/CSS aus dem Core/Base-Theme, bsp textarea-resize
    - Autodetection entfernen, ändern ala Shopware mit CLI command um Listen modules/plugins/themes zu aktualisieren
        - Dependency-Reihenfolge nach Topsort in entsprechenden Module/Plugin speichern -> einfaches Laden beim Start
- Composer basierte Plugin/Module/Theme Struktur mit composer autoload-Angabe \
    Separat kompilierte src-Dateien bleiben innerhalb des Packages \
    -> alle Plugins etc im gleichen Verzeichnis \
    -> Erweiterungsmöglichkeit zb für Vue-Apps
