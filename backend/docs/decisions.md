# Architecture Decision Records

## ADR-001: Repository + Service Pattern with SOLID
**Date:** 2026-07-01

### Context
Need a consistent architecture that enforces separation of concerns, testability, and maintainability across all entities.

### Decision
Every entity follows a strict 12-file pattern:
- Migration, Model, Repository Interface + Implementation, Service Interface + Implementation, Request, Resource, Collection, Controller, Routes, Provider

Bindings registered in `bootstrap/providers.php` via dedicated service providers.

### Consequences
- Consistent structure across all entities
- Easy to add/modify entities without affecting others
- More files per entity, but clear responsibilities
