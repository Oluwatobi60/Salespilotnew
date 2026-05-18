<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

/**
 * Authorization Trait for Business Resources
 *
 * Ensures users can only access resources belonging to their business.
 * Prevents Insecure Direct Object References (IDOR) vulnerabilities.
 *
 * Usage:
 * class CategoryController extends Controller
 * {
 *     use AuthorizesBusinessResources;
 *
 *     public function update($id)
 *     {
 *         $category = $this->findAndAuthorize(Category::class, $id);
 *         // Now safe to update $category
 *     }
 * }
 */
trait AuthorizesBusinessResources
{
    /**
     * Get the business name for the current authenticated user
     *
     * @return string
     */
    protected function getUserBusinessName(): string
    {
        $user = Auth::user();

        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        $businessName = $user->business_name;

        // If user was added by another manager, get the creator's business name
        if (isset($user->addby) && $user->addby !== null) {
            $creator = User::where('email', $user->addby)->first();
            if ($creator) {
                $businessName = $creator->business_name;
            }
        }

        return $businessName;
    }

    /**
     * Check if the current user owns this resource
     *
     * @param Model $resource The model instance to check
     * @param string $businessField The field name containing business identifier (default: 'business_name')
     * @return Model Returns the resource if authorized
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException (403) if unauthorized
     */
    protected function authorizeResource(Model $resource, string $businessField = 'business_name'): Model
    {
        $userBusinessName = $this->getUserBusinessName();

        if (!isset($resource->$businessField) || $resource->$businessField !== $userBusinessName) {
            abort(403, 'You do not have permission to access this resource');
        }

        return $resource;
    }

    /**
     * Find resource by ID and check ownership
     *
     * This is the most common method you'll use. It finds a resource by ID
     * and automatically verifies the user has permission to access it.
     *
     * @param string $modelClass The fully qualified model class name
     * @param int|string $id The resource ID
     * @param string $businessField The field name containing business identifier (default: 'business_name')
     * @return Model Returns the authorized resource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException if not found
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException (403) if unauthorized
     *
     * @example
     * $category = $this->findAndAuthorize(Category::class, $id);
     */
    protected function findAndAuthorize(string $modelClass, $id, string $businessField = 'business_name'): Model
    {
        $userBusinessName = $this->getUserBusinessName();

        $resource = $modelClass::where('id', $id)
            ->where($businessField, $userBusinessName)
            ->firstOrFail();

        return $resource;
    }

    /**
     * Get a query builder scoped to the current user's business
     *
     * Useful for listing resources.
     *
     * @param string $modelClass The fully qualified model class name
     * @param string $businessField The field name containing business identifier (default: 'business_name')
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @example
     * $categories = $this->scopedQuery(Category::class)->paginate(20);
     */
    protected function scopedQuery(string $modelClass, string $businessField = 'business_name')
    {
        $userBusinessName = $this->getUserBusinessName();

        return $modelClass::where($businessField, $userBusinessName);
    }

    /**
     * Check if current user owns a resource by manager_email field
     *
     * Some models use manager_email instead of business_name.
     *
     * @param Model $resource
     * @return Model
     */
    protected function authorizeByManagerEmail(Model $resource): Model
    {
        $user = Auth::user();

        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        if (!isset($resource->manager_email) || $resource->manager_email !== $user->email) {
            abort(403, 'You do not have permission to access this resource');
        }

        return $resource;
    }

    /**
     * Find and authorize by manager_email field
     *
     * @param string $modelClass
     * @param int|string $id
     * @return Model
     */
    protected function findAndAuthorizeByManagerEmail(string $modelClass, $id): Model
    {
        $user = Auth::user();

        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        $resource = $modelClass::where('id', $id)
            ->where('manager_email', $user->email)
            ->firstOrFail();

        return $resource;
    }

    /**
     * Check if user has permission to access staff resource
     *
     * Staff can be filtered by manager_email or business_name
     *
     * @param Model $staff
     * @return Model
     */
    protected function authorizeStaff(Model $staff): Model
    {
        $user = Auth::user();

        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        $userBusinessName = $this->getUserBusinessName();

        // Check either manager_email matches OR business_name matches
        $hasAccess = ($staff->manager_email === $user->email)
                  || ($staff->business_name === $userBusinessName);

        if (!$hasAccess) {
            abort(403, 'You do not have permission to access this staff member');
        }

        return $staff;
    }
}
