# Flujo de Trabajo - Cosmox-SAS / APIDIAN

Guía de flujo de trabajo para el fork personalizado de APIDIAN en la organización Cosmox-SAS.

---

## 📡 Remotes Configurados

```bash
# Ver remotes configurados
git remote -v
```

| Nombre | URL | Propósito |
|--------|-----|-----------|
| **origin** | `https://github.com/Cosmox-SAS/co-apidian2026.git` | **Principal** - Nuestro repo personalizado en GitHub |
| **facturalatam** | `ssh://git@git.buho.la:2224/facturalatam/co-apidian2026.git` | **Upstream** - Repo original del equipo principal |

---

## 🔄 Flujos de Trabajo

### 1. Desarrollo de Personalizaciones (Cosmox-SAS)

Cuando necesitas agregar funcionalidades personalizadas para Cosmox:

```bash
# 1. Asegurarse de estar en la última versión de master
git checkout master
git pull origin master

# 2. Crear rama de feature
git checkout -b feature/nombre-descriptivo

# Ejemplos:
git checkout -b feature/s3-integration
git checkout -b feature/custom-invoice-template
git checkout -b fix/bug-reportes

# 3. Hacer los cambios y commits
git add .
git commit -m "feat: descripción de la funcionalidad"

# 4. Subir la rama a GitHub
git push origin feature/nombre-descriptivo

# 5. Crear Pull Request en GitHub (opcional pero recomendado)
gh pr create --title "feat: nombre de la funcionalidad" \
             --body "Descripción de los cambios realizados" \
             --base master

# 6. Después de mergear, limpiar
 git checkout master
 git pull origin master
 git branch -d feature/nombre-descriptivo
```

---

### 2. Actualizar desde Facturalatam (Buho.la)

Cuando el equipo principal de facturalatam hace cambios y necesitamos actualizar nuestro fork:

#### Método A: Usando Pull Request (Recomendado)

```bash
# 1. Fetch de los cambios del equipo principal
git fetch facturalatam

# 2. Crear rama de sincronización con fecha
git checkout -b sync/facturalatam-$(date +%Y%m%d) facturalatam/master

# 3. Subir la rama a nuestro GitHub
git push origin sync/facturalatam-$(date +%Y%m%d)

# 4. Crear PR para revisar cambios antes de mergear
gh pr create --title "Sync: Update from facturalatam $(date +%Y-%m-%d)" \
             --body "Cambios del equipo principal de facturalatam que necesitan ser integrados.

- [ ] Revisar compatibilidad con nuestras personalizaciones
- [ ] Probar en ambiente de desarrollo
- [ ] Resolver conflictos si existen" \
             --base master

# 5. Después de mergear el PR en GitHub, actualizar local
git checkout master
git pull origin master

# 6. Limpiar rama temporal
git branch -d sync/facturalatam-$(date +%Y%m%d)
```

#### Método B: Directo a Master (Para cambios pequeños/confiables)

```bash
# 1. Actualizar master local desde facturalatam
git checkout master
git pull facturalatam master

# 2. Subir a nuestro GitHub
git push origin master

# ⚠️ Solo usar si estás seguro de que no hay conflictos
```

---

### 3. Verificar Cambios Pendientes

```bash
# Ver qué commits tenemos nosotros que no tiene facturalatam
git fetch facturalatam
git log facturalatam/master..origin/master --oneline

# Ver qué commits tiene facturalatam que no tenemos nosotros
git log origin/master..facturalatam/master --oneline

# Ver diferencias
git diff facturalatam/master..origin/master
```

---

### 4. Resolución de Conflictos

Si hay conflictos al mergear desde facturalatam:

```bash
# 1. Crear rama de sincronización
git fetch facturalatam
git checkout -b resolve/sync-$(date +%Y%m%d) facturalatam/master

# 2. Intentar mergear nuestros cambios
git merge origin/master

# Si hay conflictos, git mostrará los archivos conflictivos
# Editar los archivos y resolver los conflictos (marcas <<<< ==== >>>>)

# 3. Después de resolver
git add .
git commit -m "resolve: merge conflicts between facturalatam and cosmox-sas"

# 4. Subir y crear PR
git push origin resolve/sync-$(date +%Y%m%d)
gh pr create --title "resolve: sync with facturalatam resolving conflicts" --base master
```

---

## 🚀 Comandos Rápidos de Referencia

### Diarios

```bash
# Ver estado
git status

# Ver remotes
git remote -v

# Ver últimos commits
git log --oneline -10

# Ver gráfico de ramas
git log --oneline --graph --all -15
```

### Sincronización

```bash
# Alias útiles (agregar a ~/.gitconfig)
[alias]
    sync-facturalatam = !git fetch facturalatam && git log facturalatam/master..origin/master --oneline
    ahead-facturalatam = !git fetch facturalatam && git log origin/master..facturalatam/master --oneline
```

### Configuración

```bash
# Si necesitas reconfigurar los remotes:

# 1. Eliminar remotes actuales
git remote remove origin
git remote remove facturalatam

# 2. Agregar nuevos
git remote add origin https://github.com/Cosmox-SAS/co-apidian2026.git
git remote add facturalatam ssh://git@git.buho.la:2224/facturalatam/co-apidian2026.git

# 3. Verificar
git remote -v
```

---

## 📝 Convenciones de Commits

Para mantener consistencia en el historial:

```
feat: nueva funcionalidad
fix: corrección de bug
docs: cambios en documentación
style: cambios de formato (espacios, comas)
refactor: refactorización de código
test: agregar o modificar tests
chore: tareas de mantenimiento
sync: sincronización desde facturalatam
```

**Ejemplos:**
- `feat: add s3 storage configuration`
- `fix: resolve pdf generation error`
- `sync: update from facturalatam 2024-04-17`

---

## 🔄 Estrategia de Ramas

```
master (producción/stable)
  │
  ├── feature/s3-integration ────────┐
  │                                    │
  ├── feature/custom-reports ────────┼──> PR → merge to master
  │                                    │
  ├── fix/bug-critical ──────────────┘
  │
  ├── sync/facturalatam-20240417 ─────┐
                                     ├──> PR → revisar → merge to master
  ├── resolve/sync-20240417 ────────┘
```

---

## ⚠️ Notas Importantes

1. **Nunca hagas push directo a master** en cambios grandes. Usa PRs.
2. **Siempre revisa** los cambios de facturalatam antes de mergear.
3. **Mantén el DEPLOYMENT_GUIDE.md actualizado** con nuestras configuraciones específicas.
4. **El archivo .env nunca se commitea** (está en .gitignore).
5. **Backups antes de grandes cambios**: `git tag backup-$(date +%Y%m%d) && git push origin backup-$(date +%Y%m%d)`

---

## 🆘 Troubleshooting

### Problema: "Permission denied (publickey)" al hacer pull de facturalatam

```bash
# Tu clave SSH no está configurada en git.buho.la
# Contacta al admin de facturalatam para agregar tu clave pública
# O alternativamente, usa HTTPS si está disponible
```

### Problema: "fatal: refusing to merge unrelated histories"

```bash
# Si los repos tienen historias diferentes
git merge facturalatam/master --allow-unrelated-histories
```

### Problema: Cambios locales no commiteados

```bash
# Guardar cambios temporalmente
git stash

# Hacer el pull/merge
 git pull facturalatam master

# Recuperar cambios
 git stash pop
```

---

## 📞 Contactos

- **Cosmox-SAS**: [Tu equipo]
- **Facturalatam**: [Equipo principal via buho.la]

---

**Última actualización:** Abril 2026  
**Versión:** 1.0  
**Autor:** Equipo Cosmox-SAS
