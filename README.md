# Send Task To Webhook

[![Latest release](https://img.shields.io/github/release/autoritas-consulting/SendTaskToWebhook.svg)](https://github.com/autoritas-consulting/SendTaskToWebhook/releases)
[![GitHub license](https://img.shields.io/github/license/Naereen/StrapDown.js.svg)](https://github.com/autoritas-consulting/SendTaskToWebhook/blob/master/LICENSE)
[![Maintenance](https://img.shields.io/badge/Maintained%3F-yes-green.svg)](https://github.com/autoritas-consulting/SendTaskToWebhook/graphs/contributors)
[![Open Source Love](https://badges.frapsoft.com/os/v1/open-source.svg?v=103)]()
[![Downloads](https://img.shields.io/github/downloads/autoritas-consulting/SendTaskToWebhook/total.svg)](https://github.com/autoritas-consulting/SendTaskToWebhook/releases)

---

## :star: Si lo utilizas, dale una estrella en GitHub

¡Es la mejor forma de apoyar el trabajo y las mejoras continuas del plugin!

---

## 📦 Descripción

**SendTaskToWebhook** es un plugin para [Kanboard](https://kanboard.org) que permite **enviar automáticamente la información de una tarea a una URL de tipo webhook** cuando ocurre un evento determinado (por ejemplo, cuando una tarea se mueve a una columna específica, se cierra o se actualiza).

El plugin fue desarrollado por **Autoritas Consulting** y está pensado para integrarse fácilmente con sistemas externos como **n8n**, **Zapier** o cualquier API REST.

---

## ⚙️ Funcionalidad

El plugin añade una **acción automática personalizada** a Kanboard que:

* Escucha los eventos del modelo de tareas (`TaskModel`).
* Verifica el evento configurado (por ejemplo, `task.move.column`).
* Comprueba si la tarea pertenece a una **columna específica** (si se define).
* Envía los datos de la tarea al **webhook configurado** mediante una petición HTTP `POST` en formato JSON.

### Datos enviados al Webhook

El cuerpo del `POST` incluye:

```json
{
  "event": "task.move.column",
  "task_id": 123,
  "project": "Mi Proyecto",
  "task": { ...todos los detalles de la tarea... },
  "triggered_at": "2025-11-03T16:30:00Z"
}
```

---

## 🧠 Parámetros configurables

| Parámetro                                | Descripción                                                                                                                        | Obligatorio |
| ---------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------- | ----------- |
| **Webhook URL**                          | URL de destino donde se enviarán los datos de la tarea.                                                                            | ✅           |
| **Target column (only for move column)** | Columna específica que activa el webhook cuando una tarea se mueve a ella. Si se deja vacío, se activará con cualquier movimiento. | Opcional    |

---

## 🧩 Compatibilidad de eventos

Actualmente soporta los siguientes eventos de Kanboard:

* `task.create`
* `task.update`
* `task.close`
* `task.open`
* `task.move.column` ✅ *(evento principal más utilizado)*

El plugin ejecutará la acción **solo cuando el evento recibido coincida con el configurado para la acción**.
En el caso de `task.move.column`, además comprueba si la tarea se movió a la columna seleccionada.

---

## 🧾 Cambios técnicos relevantes

* Se ha reescrito el método `hasRequiredCondition()` para que compare correctamente el evento recibido (`$data['event_name']`) y valide la columna (`column_id`) antes de ejecutar la acción.
* Se ha añadido un **sistema de logging interno** (`/plugins/SendTaskToWebhook/logs/webhook.log`) para registrar todas las ejecuciones y errores del plugin.
* Se ha actualizado el **template de creación de acciones automáticas** para:

  * Mostrar correctamente los campos `Webhook URL` y `Target column`.
  * Evitar conflictos con otros plugins (como `AutomaticActionUX`).
  * Mejorar la maquetación y compatibilidad con temas personalizados.
* Ahora el plugin es completamente compatible con Kanboard ≥ 1.2.32.

---

## 🧰 Instalación

1. Crear un directorio llamado **SendTaskToWebhook** dentro de la carpeta `plugins` de Kanboard.
2. Copiar todos los archivos del repositorio en ese directorio.
3. Asegurarse de que el servidor tenga permisos de escritura en `plugins/SendTaskToWebhook/logs/`.
4. Reiniciar Kanboard o refrescar la página de administración de plugins.

---

## 🧪 Prueba rápida

1. Ve al menú **Acciones automáticas** de un proyecto.
2. Crea una nueva acción con:

   * **Evento:** *Tarea movida a otra columna (task.move.column)*
   * **Acción:** *Send Task To Webhook*
   * **Parámetros:** define la URL de webhook (por ejemplo, un webhook de n8n) y, opcionalmente, una columna específica.
3. Mueve una tarea entre columnas y revisa:

   * El log en `plugins/SendTaskToWebhook/logs/webhook.log`.
   * Que el webhook haya recibido los datos correctamente.

---

## 🧩 Ejemplo de uso con n8n

Configura un flujo de tipo **Webhook → HTTP Request → Database** en n8n, con la URL generada por tu instancia (por ejemplo):

```
https://n8n.miempresa.com/webhook/kanboard/mayores
```

Cada vez que una tarea se mueva a la columna configurada, Kanboard enviará automáticamente la información de la tarea a esa URL.

---

## 🧑‍💻 Autor

**Autoritas Consulting**
[https://autoritas.es](https://autoritas.es)

Desarrollado por **Fran Paredes**
Adaptado y mejorado para integración con **n8n / Webhooks REST**.

---

## 📄 Licencia

Este proyecto se distribuye bajo licencia **MIT**.

---
