# UIToolboxDocBundle

---

## 🎯 Objectif

Fournir une interface de démonstration et de documentation pour tous les composants disponibles dans `ui-toolbox`.

---

## 📦 Installation

Ajoutez le bundle dans votre projet :

```bash
composer require appolodev/ui-toolbox-docs
```

---

## 🛠️ Activation des routes

Créez un fichier `config/routes/dev/ui_toolbox.yaml` avec le contenu suivant :

```yaml
when@dev:
    ui_toolbox:
        resource: '@UIToolboxDocBundle/config/routes/documentation.yaml'
        prefix: /_uitoolbox
        type: yaml
```

Cela permet de déclarer dynamiquement les routes de documentation **uniquement en environnement de développement**.

---

## 🧪 Exemple d’utilisation

Rendez-vous sur [`/_uitoolbox`](http://localhost:8000/_uitoolbox) pour découvrir les composants disponibles et leur rendu en conditions réelles.
